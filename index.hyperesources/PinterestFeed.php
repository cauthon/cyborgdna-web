<?php
class PinterestFeed extends CWidget
{
    /**
	 * Pinterest username
	 */
	public $username;
    
	/**
	 * record limit
	 */
	public $limit;
    
    /**
	 * offset
	 */
	public $site;
    
    public $RSS_Content = array();
    
	public function init()
	{
        // Initiliaze parameters
        if (!empty($this->username)) $this->username = 'http://pinterest.com/'.$this->username.'/feed.rss';
		if (empty($this->site)) $this->site = 0;
	}
    
    public function run()
	{
        $this->RSS_Retrieve($this->username);
        if ($this->limit > 0) {
            $this->RSS_Content = array_slice($this->RSS_Content, $this->site, $this->limit + 1 - $this->site);
        }
	}
    
    public function RSS_Retrieve($url)
    {
        $doc = new DOMDocument();
        $doc->load($url);

        $channels = $doc->getElementsByTagName("channel");

        foreach ($channels as $channel) {
            $this->RSS_Channel($channel);
        }
    }
    
    public function RSS_Channel($channel)
    {
        $items = $channel->getElementsByTagName("item");

        // Processing channel
        $y = $this->RSS_Tags($channel, 0);  // get description of channel, type 0
        array_push($this->RSS_Content, $y);

        // Processing articles
        foreach ($items as $item) {
            $y = $this->RSS_Tags($item, 1); // get description of article, type 1
            array_push($this->RSS_Content, $y);
        }
    }
    
    public function RSS_Tags($item, $type)
    {
        $y = array();
        $tnl = $item->getElementsByTagName("title");
        $tnl = $tnl->item(0);
        $title = $tnl->firstChild->textContent;

        $tnl = $item->getElementsByTagName("link");
        $tnl = $tnl->item(0);
        $link = $tnl->firstChild->textContent;

        $tnl = $item->getElementsByTagName("pubDate");
        $tnl = $tnl->item(0);
        $date = $tnl->firstChild->textContent;

        $tnl = $item->getElementsByTagName("description");
        $tnl = $tnl->item(0);
        $description = $tnl->firstChild->textContent;
        
        $tal = explode('src="',$description);
     
        // fetch image from description
        if(count($tal) > 1) {
            $imagen = explode('.jpg',$tal[1]);
            $imagen = $imagen[0].".jpg";
        } else {
            $imagen = '';
        }

        $y["title"] = $title;
        $y["link"] = $link;
        $y["date"] = $date;
        $y["description"] = $description;
        $y["type"] = $type;
        $y["image"] = $imagen;
        
        return $y;
    }
}
?>