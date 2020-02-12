<?php
/**
 */

/**
 * Basic class which provides all functions to retrieve and paginate feeds
 */
class NewsFeed {

  /**
   * @var array $data
   */
  protected $data;

  /**
   * @var int $itemsPerPage
   */
  protected $itemsPerPage;

  function __construct($data, $itemsPerPage) {
    $this->data = $data;
    $this->itemsPerPage = $itemsPerPage;
  }

  /**
   * Returns the pictures of the given page or an empty array if page doesn't exist
   * @param int $page
   * @return array
   */
  public function getPage($page=1) {
    if ($page > 0 && $page <= $this->getNumberOfPages()) {
      $startOffset = ($page - 1) * $this->itemsPerPage;
      return array_slice($this->data, $startOffset, $this->itemsPerPage);
    }
    return array();
  }

  /**
   * Returns the maximum number of pages
   * @return int
   */
  public function getNumberOfPages() {
    return ceil(count($this->data) / $this->itemsPerPage);
  }
}

 function GetNewsList()
{
    $tmpdata = array();
    include ("inc_whcc_functions.php"); 

    $curNewsItem=isset($_REQUEST['mid']) ? $_REQUEST['mid'] : 0;

    $cnt=1;
    $pr_cnt=1; //safety counter
    while ($cnt<=30)
    {
        $newsItem= GetNewsItem($curNewsItem);

        if (trim($newsItem['title'])<>'')
        {
            $tmpdata[] = $newsItem;
              $cnt=$cnt+1;
        } else {
            $pr_cnt=$pr_cnt+1; //safety counter
            if ($pr_cnt>300)
            {
                $cnt=99; //kick out of while loop
            }
        }

        $curNewsItem=$curNewsItem-1;

    }
            


    return $tmpdata;
}

// Our data source
$data = GetNewsList();



// Create instance of Ad database with 8 items per page and our data as source
$NewsFeed = new NewsFeed($data, 8);

$result = array(
  'success' => TRUE,
  'message' => 'Retrieved News Feeds',
  'data' => array()
);

$callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : false;

// Get requested page number from request and return error message if parameter is not a number
$page = 1;
try {
//  $page = intval($_REQUEST['page']);
  $page = intval(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
    
} catch (Exception $e) {
  $result['success'] = FALSE;
  $result['message'] = 'Parameter page is not a number';
}

// Get data from database
$result['data'] = $NewsFeed->getPage($page);

if (count($result['data']) == 0 || $page >= $NewsFeed->getNumberOfPages()) {
  $result['success'] = TRUE;
  $result['message'] = 'No more feeds';
}

// Encode data as json or jsonp and return it
if ($callback) {
  header('Content-Type: application/javascript');
  echo $callback.'('.json_encode($result).')';
} else {
  header('Content-Type: application/json');
  echo json_encode($result);
}
