<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Models\Reviews;

class StructuredDataController extends ReviewController
{
    public $data;
    public $reviews;

    /**
     * Register the class so when we call this method we print the structured data in the footer
     */
    public function register()
    {
        $this->setup();
        $this->addActions();
    }

    public function setup() {

        if (Plugin::hasSetting('authorization')) {
            $this->reviews  = new Reviews();
            $data           = $this->getCachedReviews();

            $this->reviews->setData($data);
            $this->data = $this->reviews->data;
        }
    }

    public function addActions()
    {
        add_action('wp_footer', [$this, 'print']);
    }

    /**
     * Print method to add the structured data to the footer of the website
     * We print the html
     * @return void
     */
    public function print()
    {
        $html = '<script type="application/ld+json">
            {
            "@context": "http://schema.org",
            "@type": "Product",
            "name": "'.get_bloginfo('name').'",';

        if ($string = get_bloginfo('description')):
            $html .= '"description": "'.$string.'",';
        endif;

        $html .= '"brand": "'.get_bloginfo('name').'"';
        
        if (!empty($this->data) && $this->reviews->hasReviews()) {
            $html .= ',
                "aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "'.$this->reviews->getRatingValue().'",
                    "bestRating": "'.$this->reviews->getBestRating().'",
                    "worstRating": "'.$this->reviews->getWorstRating().'",
                    "reviewCount": "'.$this->reviews->getReviewCount().'"
                },
                "review": [';
            
            $lastKey = array_key_last($this->reviews->reviews());
            foreach ($this->reviews->reviews() as $key => $review):
                $html .= '{
                    "@context": "http://schema.org/",
                    "@type": "Review",
                    "name": "'.$review['name'].'",
                    "reviewBody": "'.$this->cleanComment($review['reviewBody']).'",
                    "reviewRating": {
                        "@type": "Rating",
                        "ratingValue": "'.$review['ratingValue'].'"
                    },
                    "datePublished": "'.$review['datePublished'].'",
                    "author": {
                        "@type": "Person",
                        "name": "'.$review['author_name'].'"
                    }';
                $html .= ($lastKey === $key ? '}' : '},');
            endforeach;

            $html .= ']';
        }

        $html .= '}
        </script>';

        // Remove all new lines
        $html = preg_replace('/\r|\n/', '', $html);

        print($html);
    }
    
    /**
     * Remove all quotes that Google places around translations
     * Remove all new lines
     *
     * @param  string $comment
     * @return string
     */
    protected function cleanComment(string $comment) : string
    {
        $comment = str_replace(['"', '“'], '', $comment);
        $comment = preg_replace('/\r|\n/', '', $comment);
        return $comment;
    }
}