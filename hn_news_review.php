<html>
<? 
        include ("inc_whcc_functions.php"); 
?>

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<!-- Global CSS for the page and tiles -->
  <link rel="stylesheet" href="css/main.css">
  <!-- //Global CSS for the page and tiles -->
  <!-- CSS Reset -->
  <link rel="stylesheet" href="css/reset.css">
<body>

  <div id="container">
    <div class="main">
        <div class="contact-form" style="text-align: center;">
            <h3>Hacker News Network Feeds</h3>
        </div>

      <ul id="tiles">
        <!-- Active Ad list -->
      </ul>

      <div id="loader">
        <div id="loaderCircle"></div>
      </div>

        <BR><BR>
    </div>

  </div>

  <!----wookmark-scripts---->
  <!-- include jQuery -->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.imagesloaded.js"></script>
    <script src="js/jquery.wookmark.js"></script>

<?
$maxId=GetMaxNewsItem();
echo "Retrieving News Feeds (".$maxId.") ......<br>";
    $use_db=isset($_REQUEST['db']) ? $_REQUEST['db'] : 0;

    if ($use_db==1)
    {
        $link_apiURL = 'inc_whcc_curl_db.php?mid='.$maxId;
    } else
    {
        $link_apiURL = 'inc_whcc_curl.php?mid='.$maxId;
    }
?>
    
  <!-- Once the page is loaded, initialize the plug-in. -->
  <script type="text/javascript">
    (function ($) {
      var $tiles = $('#tiles'),
          $handler = $('li', $tiles),
          page = 1,
          isLoading = false,
          apiURL = '<?=$link_apiURL?>',
          lastRequestTimestamp = 0,
          fadeInDelay = 500,
          $window = $(window),
          $document = $(document);

      // Prepare layout options.
      var options = {
          itemWidth: 800, // Optional min width of a grid item
          autoResize: true, // This will auto-update the layout when the browser window is resized.
          container: $('#tiles'), // Optional, used for some extra CSS styling
          offset: 40, // Optional, the distance between grid items
          outerOffset: 100, // Optional the distance from grid to parent
          flexibleWidth: '50%' // Optional, the maximum width of a grid item
        };

      /**
       * When scrolled all the way to the bottom, add more tiles.
       */
      function onScroll(event) {
        // Only check when we're not still waiting for data.
        if (!isLoading) {
          // Check if we're within 100 pixels of the bottom edge of the browser window.
          var closeToBottom = ($window.scrollTop() + $window.height() > $document.height() - 100);
          if (closeToBottom) {
            // Only allow requests every half second
            var currentTime = new Date().getTime();
            if (lastRequestTimestamp < currentTime - 500) {
              lastRequestTimestamp = currentTime;
              loadData();
            }
          }
        }
      };

      /**
       * Refreshes the layout.
       */
      function applyLayout($newHeadlines) {
        options.container.imagesLoaded(function() {
          // Destroy the old handler
          if ($handler.wookmarkInstance) {
            $handler.wookmarkInstance.clear();
          }

          // Create a new layout handler.
          $tiles.append($newHeadlines);
          $handler = $('li', $tiles);
          $handler.wookmark(options);



          
          
          // Set opacity for each new image at a random time
          $newHeadlines.each(function() {
            var $self = $(this);
            window.setTimeout(function() {
              $self.css('opacity', 1);
            }, Math.random() * fadeInDelay);
          });
        });
      };

      /**
       * Loads data from the API.
       */
      function loadData() {
        isLoading = true;
        $('#loaderCircle').show();
//    alert("begin12");

        $.ajax({
          url: apiURL,
          dataType: 'json', // Set to jsonp if you use a server on a different domain and change it's setting accordingly
          data: {page: page}, // Page parameter to make sure we load new data
          success: onLoadData
        });
      };

      /**
       * Receives data from the API, creates HTML for images and updates the layout
       */
      function onLoadData(response) {
        isLoading = false;
        $('#loaderCircle').hide();
//    alert("begin");

        // Increment page index for future calls.
        page++;

        // Create HTML for the images.
        var html = '',
            data = response.data,
            tcountry = '',
            i = 0, length = data.length, image, opacity,
            $newHeadlines;
    
        function getDisplayTime(UNIX_timestamp){
            var a = new Date(UNIX_timestamp * 1000);
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            var year = a.getFullYear();
            var month = months[a.getMonth()];
            var date = a.getDate();
            var hour = a.getHours();
            var min = a.getMinutes();
            var sec = a.getSeconds();
            var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
            return time;
          }
        function cleanText(value) {
        //This function was added April 2018 to try to fix bad characters in the header text but ultimately did not work
        //character> m ord num>109
        //character> � ord num>226
        //character> star ord num>9733 - found 7/8/18
            var outval='';
            for (var i = 0; i < value.length; i++) {
                var code = value.charCodeAt(i);
                if ((code>=32) && (code<=126)){
                    outval+=value.charAt(i);
                } else
                {
                //    alert(code+" bad "+value);
                }
            }
            return outval;
        }


        for (; i < length; i++) {
          project = data[i];

          var formattedTime = getDisplayTime(project.time);
          
          html += '<li>';
          html += '<div  >';

          html += '  <div class="post-info">';
          html += '         <div class="post-basic-info">';
          html += '             <h3><a href="'+project.url+'" target=_blank>'+project.title+'</a></h3>'; 
          html += '             &nbsp;&nbsp;'+project.by+'&nbsp;&nbsp;';
          html += '             &nbsp;&nbsp;'+formattedTime+'&nbsp;&nbsp;';
          html += '         </div>';
          html += '  </div>';
          html += '  </div>';
          html += '  <div class="clear"> </div>';
          html += '</li>';
        }

        $newHeadlines = $(html);

        // Disable requests if we reached the end
        if (response.message == 'No more pictures') {
          $document.off('scroll', onScroll);
        }

        // Apply layout.
        applyLayout($newHeadlines);
      };

      // Capture scroll event.
      $document.on('scroll', onScroll);

      // Load first data from the API.
      loadData();
    })(jQuery);
  </script>

</body>
</html>
