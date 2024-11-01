<?php namespace Tussendoor\GmbReviews\Helpers;

use Tussendoor\GmbReviews\Plugin;

class Notice
{
    protected $state;
    protected $message;
    protected $priority = 10;

    private $transientId;

    public function __construct()
    {
        $this->transientId = Plugin::config('plugin.tag') . '_notices';
    }

    /**
     * The contents of the notice are considered as a failed action.
     * @param  string $message
     * @return $this
     */
    public function failed($message = null)
    {
        if (!empty($message)) {
            $this->setMessage($message);
        }

        return $this->setState(2);
    }

    /**
     * The contents of the notice are considered as a successful action.
     * @param  string $message
     * @return $this
     */
    public function successful($message = null)
    {
        if (!empty($message)) {
            $this->setMessage($message);
        }

        return $this->setState(1);
    }

    /**
     * The contents of the notice are considered as a successful action.
     * @param  string $message
     * @return $this
     */
    public function warning($message = null)
    {
        if (!empty($message)) {
            $this->setMessage($message);
        }

        return $this->setState(3);
    }

    /**
     * Wether the notice should be displayed as an success or as an error.
     * @param bool $success
     */
    public function setState($state)
    {
        switch ($state) {
            case 1:
                $this->state = 'succes';
                break;
        
            case 2:
                $this->state = 'error';
                break;
            
            case 3:
                $this->state = 'warning';
                break;
                
            case 4:
                $this->state = 'info';
                break;
                
            default:
                $this->state = 'info';
                break;
        }

        return $this;
    }

    /**
     * Set the message that's being displayed to the user.
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the priority of the notice, which sets the action priority. A higher
     * priority means the notice will be displayed lower.
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = (int) $priority;

        return $this;
    }

    /**
     * Create the notice. Stores it within the transient table,
     * as the user is redirected after a post has been saved.
     * @return bool
     */
    public function create()
    {
        $notices = $this->get();
        $messageHtml = $this->generateHtml();

        array_push($notices, $messageHtml);

        return $this->save($notices);
    }

    /**
     * Display all stored notices. If none are found, this method returns false.
     * This method deletes all stored notices after it has finished running.
     * @return bool
     */
    public function display()
    {
        $notices = $this->get();

        if (empty($notices)) {
            return false;
        }

        foreach ($notices as $notice) {
            $this->setNoticeAction($notice);
        }

        return $this->delete();
    }

    /**
     * Generate the notice HTML. Uses WordPress css classes
     * @return string
     */
    protected function generateHtml()
    {
        return sprintf('
            <div class="notice notice-%s is-dismissible is-dismissible mx-0 py-0 rounded">
                <p>%s</p>
            </div>
        ', $this->state, $this->message);
    }

    /**
     * Set the action within WordPress used for Admin notices
     * @param string $notice
     */
    protected function setNoticeAction($notice)
    {
        add_action('all_admin_notices', function () use ($notice) {
            echo wp_kses_post($notice);
        }, $this->priority);
    }

    /**
     * Get all stored notices from the transients table.
     * @return array
     */
    private function get()
    {
        $existing = get_transient($this->transientId);

        return $existing === false || empty($existing) ? [] : array_unique($existing);
    }

    /**
     * Save a single notice to a transient. The transient is stored for 60 seconds.
     * @param  mixed $messages
     * @return bool
     */
    private function save($messages)
    {
        return set_transient($this->transientId, $messages, 60);
    }

    /**
     * Delete all notice transients, identified by our own unique identifier.
     * @return bool
     */
    private function delete($force = false)
    {
        if ($force) {
            return delete_transient($this->transientId);
        }

        return add_action('all_admin_notices', function () {
            return delete_transient($this->transientId);
        }, $this->priority);
    }

    /**
     * Add error code to the message
     *
     * @param  string $code
     * @return $this
     */
    public function errorCode(string $code)
    {
        $this->message = $this->message . ' <strong>Error code: ' . $code . ' </strong>';
        return $this;
    }
}
