<?php namespace Tussendoor\GmbReviews\Models;

class Reviews
{    
    /**
     * Set the data we work with
     *
     * @param  array $data
     * @return void
     */
    public function setData(array $data) : void
    {
        $this->data = $data;
    }

    /**
     * Get single value from the data
     *
     * @return string
     */
    public function getRatingValue() : string
    {
        return isset($this->data['averageRating']) ? $this->data['averageRating'] : '';
    }

     /**
      * Get single value from the data
      *
      * @return string
      */
     public function getRatingPercentage() : int
     {
        if (empty($this->getRatingValue())) {
            return 100;
        }

        $percentage = ($this->getRatingValue() / $this->getBestRating() * 100);
        return $percentage;
     }
    
    /**
     * Get single value from the data
     *
     * @return string
     */
    public function getBestRating() : int
    {
        return 5;
    }
    
    /**
     * Get single value from the data
     *
     * @return string
     */
    public function getWorstRating() : int
    {
        return 1;
    }
    
    /**
     * Get single value from the data
     *
     * @return int
     */
    public function getReviewCount() : int
    {
        return isset($this->data['totalReviewCount']) ? (int) $this->data['totalReviewCount'] : 0;
    }
    
    /**
     * Check if there are reviews to be found
     *
     * @return bool
     */
    public function hasReviews() : bool
    {
        return !empty($this->reviews());
    }
    
    /**
     * Get review data and add them to an formatted array to loop over
     *
     * @return array
     */
    public function reviews() : array
    {
        $reviews = (isset($this->data['reviews']) ? $this->data['reviews'] : []);
        
        $formattedReviews = [];
        foreach ($reviews as $review) {            
            if (is_array($review)) {
                $formattedReviews[] = $this->formatReview($review);
            }
        }
    
        return $formattedReviews;
    }
    
    /**
     * Format the review data to a more workable array
     *
     * @param  array $review raw review data from te request
     * @return null|array
     */
    public function formatReview(array $review) : ?array
    {
        if (empty($review)) return null;
        
        $rating = (isset($review['starRating']) ? $this->transformRatingToString($review['starRating']) : '' );
        $datePublished = (isset($review['createTime']) ? $this->transformDate($review['createTime'], 'd-m-Y') : '' );

        $comment   = (isset($review['comment']) ? $review['comment'] : '');
        $reply     = (isset($review['reviewReply']['comment']) ? $review['reviewReply']['comment'] : '');
        $replyDate = (isset($review['reviewReply']['updateTime']) ? $this->transformDate($review['reviewReply']['updateTime'], 'd-m-Y') : '');

        $formattedReview = [
            'name'          => (isset($review['name']) ? $review['name'] : ''),
            'reviewBody'    => $comment,
            'ratingValue'   => $rating,
            'datePublished' => $datePublished,
            'author_name'   => $review['reviewer']['displayName'],
            'reply'         => $reply,
            'replyDate'     => $replyDate,
        ];
    
        return $formattedReview;
    }
    
    /**
     * Get enum rating and transform it into a string
     *
     * @return string
     */
    protected function transformRatingToString($rating) : string
    {
        switch ($rating) {
            case 'FIVE':
                return '5';
                break;
            case 'FOUR':
                return '4';
                break;
            case 'THREE':
                return '3';
                break;
            case 'TWO':
                return '2';
                break;
            case 'ONE':
                return '1';
                break;
            case 'STAR_RATING_UNSPECIFIED':
                return '0';
                break;
            default:
                return '0';
                break;
        }
    }
    
    /**
     * Transform Zulu date format in simple d-m-Y
     * @return string
     */
    public function transformDate($date, string $format) : string
    {
        $dateTime = new \DateTime($date);
        return $dateTime->format($format);
    }
}