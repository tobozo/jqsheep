<?

$credits = "
        - Tatsutoshi Nomura's 1994 'Sheep' for PC -
        - Fuji Television Animation 'Stray Sheep Poe' -
        - RIP Trivia  Village Center, Inc : Streisand Effect -
        - Gif Sprite : http://spritedatabase.net/file/12735 -
        - Help file : http://mentadd.com/sheep/ -
        - Imagemagick 'convert'  + Microsoft Gif Animator -
";

if('jquery.sheep.js' == end(explode('/', $_SERVER['REQUEST_URI']))) {
  ob_start('ob_gzhandler');
  header("Content-Type: text/javascript");
  if(!function_exists('json_encode')) {
    /* http://mike.teczno.com/JSON/JSON.phps */
    require('json.php');
  }
?>
  /********************************

    jQuery Sheep Plugin
    another useless plugin brought
    to you by tobozo
    copyleft (c+) 2013
    twitter : @TobozoTagada
    source  : http://phpsecure.info/sheep.php
    Credits :

<?=$credits;?>

  ********************************/
;(function($){

   $.fn.sheep = function(options) {
      options = $.extend({}, $.fn.sheep.defaultOptions, options);
      if(!window.sheeps) {
        window.sheeps = {
          index : [],
          sound : 'on',
          sheepCount : [],
          allSheepIDs : [],
          imgData: $.fn.sheep.initImages(),
          transform:$.fn.sheep.detectTransform2d(),
          transformOrigin : $.fn.sheep.detectTransformOrigin()
        };
        $.fn.sheep.checkSounds();
        $.fn.sheep.initSheeps();
      }
      this.each(function() {
        var element = $(this),
            randomSheepId = $.fn.sheep.getRandomSheep(),
            uniqueSheepId = $.fn.sheep.getUniqueId(randomSheepId),
            position,
            posCss = {},
            positions     = ['top', 'right', 'bottom', 'left'], // clockwise
            posId         = Math.floor(Math.random()*4)
        ;
        if(options.sheepType) {
            randomSheepId = options.sheepType;
            $.fn.sheep.removeId( uniqueSheepId );
            uniqueSheepId = $.fn.sheep.getUniqueId(options.sheepType);
        }
        if(options.position) {
          position = options.position;
        } else {
          position = positions[posId];
        }
        if(window.sheeps.transform=='' || window.sheeps.transformOrigin=='') {
          position = 'top';
        }
        if(randomSheepId=='sheep-burn') {
          if(position!='top' && position!='bottom') {
            position = (Math.random() >=0.5) ? 'top' : 'bottom';
          }
        }

        $(this).append('<div id="'+uniqueSheepId+'" class="sheep sheep-'+randomSheepId+'"></div>');

        switch(position) {
          case 'top':
            var mTop  = 0 - parseInt( element.css('border-top-width').split('px')[0] );
            var mLeft = 0 - parseInt( element.css('border-left-width').split('px')[0] );
            $('#'+uniqueSheepId).css({
              backgroundImage: "url("+window.sheeps.imgData[randomSheepId].data+")",
                    marginTop: mTop,
                     position: "absolute",
                       height: window.sheeps.imgData[randomSheepId].height,
                        width: window.sheeps.imgData[randomSheepId].width,
                         left: mLeft,
                          top: (0 - window.sheeps.imgData[randomSheepId].height)
            });
          break;

          case 'bottom':
            var mBottom = 0 - parseInt( element.css('border-bottom-width').split('px')[0] );
            var mLeft = 0 - parseInt( element.css('border-left-width').split('px')[0] );
            posCss[window.sheeps.transform] = 'rotate(180deg)';
            $('#'+uniqueSheepId).css({
              backgroundImage: "url("+window.sheeps.imgData[randomSheepId].data+")",
                 marginBottom: mBottom,
                     position: "absolute",
                       height: window.sheeps.imgData[randomSheepId].height,
                        width: window.sheeps.imgData[randomSheepId].width,
                         zoom: 1,
                         left: mLeft,
                       bottom: (0 - window.sheeps.imgData[randomSheepId].height)
            }).css(posCss);
          break;

          case 'left':
            var mLeft = 0 - parseInt( element.css('border-left-width').split('px')[0] );
            var mTop  = 0 - parseInt( element.css('border-top-width').split('px')[0] );
            posCss[window.sheeps.transform]       = 'rotate(-90deg)';
            posCss[window.sheeps.transformOrigin] = 'right middle';
            $('#'+uniqueSheepId).css({
              backgroundImage: "url("+window.sheeps.imgData[randomSheepId].data+")",
                   marginLeft: mLeft,
                     position: "absolute",
                       height: window.sheeps.imgData[randomSheepId].height,
                        width: window.sheeps.imgData[randomSheepId].width,
                         zoom: 1,
                         left: (0 - window.sheeps.imgData[randomSheepId].height),
                          top: mTop
            }).css(posCss);
          break;

          case 'right':
            var mRight = 0 - parseInt( element.css('border-right-width').split('px')[0] );
            var mTop  = 0 - parseInt( element.css('border-top-width').split('px')[0] );
            posCss[window.sheeps.transform]       = 'rotate(90deg)';
            posCss[window.sheeps.transformOrigin] = '100% 100%';
            $('#'+uniqueSheepId).css({
              backgroundImage: "url("+window.sheeps.imgData[randomSheepId].data+")",
                  marginRight: mRight,
                     position: "absolute",
                       height: window.sheeps.imgData[randomSheepId].height,
                        width: window.sheeps.imgData[randomSheepId].width,
                        right: 0,
                         zoom: 1,
                          top: mTop
            }).css(posCss);
          break;
        }
        $('#'+uniqueSheepId).data('position', position);
        if(typeof options.coords !="undefined") {
          $('#'+uniqueSheepId).css( options.coords );
          $('#'+uniqueSheepId).data('coords', options.coords);
        }
        $.fn.sheep.initSound({id:uniqueSheepId,type:randomSheepId});
        $.fn.sheep.initLoop({
          id:uniqueSheepId,
          type:randomSheepId,
          position:position
        });
      });
      return this;
   };
   $.fn.sheep.defaultOptions = {
     //class: 'sheep',
     text      : 'Meeeeeh',
     soundPath : '/snd/'
   };
   $.fn.sheep.initImages = function() {
<? if(file_exists('img/sheep/icon.png')) { ?>
     try {
         var link = document.createElement('link');
         link.type = 'image/x-icon';
         link.rel = 'shortcut icon';
         link.href = 'data:image/png;base64,<?=base64_encode(file_get_contents('img/sheep/icon.png'));?>';
         document.getElementsByTagName('head')[0].appendChild(link);
     } catch(e) { ; }
<? } ?>
     // this object was generated using php+json, see http://phpsecure.info/sheep.php?view_source
<?
      $tab = "      ";
      $cd = getcwd();
      chdir('img/sheep');
      $sheeps = glob("*.gif");

      $jsonSheeps = array();

      foreach($sheeps as $sheep) {
        // if($sheep=='flower.gif') continue;
        $imgsize = getImagesize($sheep);
        $jsonSheeps[current(explode('.', $sheep))] = array(
          "width"    => $imgsize[0],
          "height"   => $imgsize[1],
          "duration" => getGIFDuration($sheep),
          "data"     => 'data:image/gif;base64,'.base64_encode(file_get_contents($sheep))
        );
      }
      chdir($cd);
      /* yeah it sucks to use the old php4 jsonless binary :-) */
      if(!function_exists('json_encode')) {
        $json = new Services_JSON();
        $jsonText = $json->encode($jsonSheeps);
      } else {
        $jsonText = $json_encode($jsonSheeps);
      }
?>
     return <?=$jsonText;?>;
   };

   $.fn.sheep.detectTransform2d = function() {
     var prefixes = 'transform WebkitTransform MozTransform OTransform msTransform'.split(' '),
           el = document.createElement('div'),
           transform2dSupport=0;
     for(var i=0; i < prefixes.length ; i++) {
       if(document.createElement('div').style[prefixes[i]] != undefined) {
         return prefixes[i];
       }
     }
     return transform2dSupport;
   }

   $.fn.sheep.detectTransformOrigin = function() {
     var prefixes = 'transformOrigin WebkitTransformOrigin MozTransformOrigin OTransformOrigin msTransformOrigin'.split(' '),
           el = document.createElement('div'),
           transformOriginSupport=0;
     for(var i=0; i < prefixes.length ; i++) {
       if(document.createElement('div').style[prefixes[i]] != undefined) {
         return prefixes[i];
       }
     }
     return transformOriginSupport;
   }

   $.fn.sheep.initSheeps = function() {
     for(var sheep in window.sheeps.imgData) {
       window.sheeps.index.push(sheep);
     }
   };

   $.fn.sheep.initSound = function(sheepObj) {
     if(window.sheeps.sound!='on') return;
     /* 1/3 chance to assign a sound trigger */
     if(Math.random()>0.66) return;
     $('#'+sheepObj.id).css({cursor:'pointer'}).click(function() {
       var sndArray = ['yawn', 'meh', 'tchu', ''];
       var rnd = Math.floor(Math.random()*4);
       var soundName = sndArray[rnd];
       /* 1/4 chance to play nothing */
       if(soundName=='') return false;
       $.fn.sheep.playSound(soundName);
     });
   };

   $.fn.sheep.playSound = function(soundName) {
       $('#sheepAudio').remove();
       $('.sheepAudioHolder').remove();
       if(window.sheeps.sound!='on') return;
       var soundPath = $.fn.sheep.defaultOptions.soundPath,
           audioHtml =
         '<audio nocontrols autoplay id="sheepAudio">'
        +'<source src="'+ soundPath + soundName + '.ogg" type="audio/ogg" />'
        +'<source src="'+ soundPath + soundName + '.mp3" type="audio/mp3" />'
        +'<source src="'+ soundPath + soundName + '.wav" type="audio/wav" />'
        +'</audio>';
       $('<div class="sheepAudioHolder"></div>').css({
         width:1,
         height:1,
         position:"absolute",
         left:0,
         top:0,
         overflow:'hidden'
       }).html(audioHtml).appendTo('body');
   };

   $.fn.sheep.checkSounds = function() {
     var a = document.createElement('audio'), 
         supportsAudio = !!(a.canPlayType),
            soundNames = ['yawn', 'meh', 'tchu'],
             soundPath = $.fn.sheep.defaultOptions.soundPath;
     if(!supportsAudio) {
       window.sheeps.sound = 'off';
       return 'off';
     }
     for(var i=0; i < soundNames.length ; i++) {
       if(   !$.fn.sheep.checkUrl(soundPath + soundNames[i] + '.mp3')
          || !$.fn.sheep.checkUrl(soundPath + soundNames[i] + '.ogg')
          || !$.fn.sheep.checkUrl(soundPath + soundNames[i] + '.wav')  ) {
         window.sheeps.sound = 'off';
         return 'off';
       }
     }
     return 'on';
   }

   $.fn.sheep.checkUrl = function(url) {
     try { 
       var http = new XMLHttpRequest(), ret;
       http.open('HEAD', url, false);
       http.send();
       ret = http.status!=404;
     } catch(e) {
       return false;
     }
     return ret;
   }


   $.fn.sheep.initLoop = function(sheepObj) {
     try {
       var duration = window.sheeps.imgData[sheepObj.type].duration * 1000;
       var sheepCoords = $('#'+sheepObj.id).data('coords');

       var anim = sheepCoords || {opacity:1};
       var parent  = $('#'+sheepObj.id).parent();
       var offset  = $('#'+sheepObj.id).offset();
       var posCss = {};

       switch(sheepObj.type) {
         case 'sheep-jumptoright':
         case 'sheep-rolltoright':
           duration = duration *2;
           switch(sheepObj.position) {
             case 'top':
               var leftPos =
                  parseFloat(parent.width())
                + parseFloat(parent.css('border-right-width').split('px')[0])
                + parseFloat(parent.css('border-left-width').split('px')[0])
                - parseFloat(window.sheeps.imgData[sheepObj.type].width)
               ;
               anim = {left:leftPos};
             break;
             case 'bottom':
               var leftPos = 0 -parseFloat(parent.css('border-left-width').split('px')[0])
               anim = {left:leftPos};
             break;
             case 'left':
               var topPos = 0 -parseFloat(parent.css('border-top-width').split('px')[0])
               anim = {top:topPos};
             break;
             case 'right':
               var topPos =
                  parseFloat(parent.height())
                + parseFloat(parent.css('border-top-width').split('px')[0])
                + parseFloat(parent.css('border-bottom-width').split('px')[0])
                - parseFloat(window.sheeps.imgData[sheepObj.type].height)
               ;
               anim = {top:topPos};
             break;
           }
           $('#'+sheepObj.id).data('coords', anim);
         break;
         case 'sheep-runtoleft':
         case 'sheep-rolltoleft':
         case 'blacksheep-runleft':
           duration = duration *2;
           switch(sheepObj.position) {
             case 'top':
               var leftPos = 0 -parseFloat(parent.css('border-left-width').split('px')[0])
               anim = {left:leftPos};
             break;
             case 'bottom':
               var leftPos =
                  parseFloat(parent.width())
                + parseFloat(parent.css('border-right-width').split('px')[0])
                + parseFloat(parent.css('border-left-width').split('px')[0])
                - parseFloat(window.sheeps.imgData[sheepObj.type].width)
               ;
               anim = {left:leftPos};
             break;
             case 'right':
               var topPos = 0 -parseFloat(parent.css('border-top-width').split('px')[0])
               anim = {top:topPos};
             break;
             case 'left':
               var topPos =
                  parseFloat(parent.height())
                + parseFloat(parent.css('border-top-width').split('px')[0])
                + parseFloat(parent.css('border-bottom-width').split('px')[0])
                - parseFloat(window.sheeps.imgData[sheepObj.type].height)
               ;
               anim = {top:topPos};
             break;
           }
           $('#'+sheepObj.id).data('coords', anim);
         break;
         case 'sheep-yawnstoright':
           if(Math.random()>0.94) {
             $.fn.sheep.playSound('yawn');
           }
         break;
         case 'sheep-sneeze':
           if(Math.random()>0.94) {
             $.fn.sheep.playSound('tchu');
           }
         break;

         case 'sheep-eat':
           var $flower = window.sheeps.imgData['flower'];
           switch(sheepObj.position) {
             case 'top':
               $('#'+sheepObj.id).parent().sheep({
                position:'top',
                coords:{left:($('#'+sheepObj.id).css('left').split('px')[0] - $flower['width'])},
                sheepType:'flower'
               });
             break;
             case 'left':

             break;
             case 'bottom':

             break;
             case 'right':

             break;
           }
         break;
         case 'flower':
           // 
         break;

         case 'sheep-burn':
           switch(sheepObj.position) {
              case 'top':
                anim= {
                  left: 0 - (offset.left - (window.sheeps.imgData[sheepObj.type].width*2)),
                  top: $(window).height() - offset.top - (window.sheeps.imgData[sheepObj.type].height*2),
                  opacity : 0
                };
              break;
              case 'bottom':
                anim= {
                  left: $(window).width() - offset.left - (window.sheeps.imgData[sheepObj.type].width*2),
                  top: $(window).height() - offset.top - (window.sheeps.imgData[sheepObj.type].height*2),
                  opacity : 0
                };
                posCss[window.sheeps.transform] = 'scaleX(-1)';
                $('#'+sheepObj.id).css(posCss);
              break;
           }
         break;

         default:
           while(duration <= 1000) {
             duration = duration *2;
           }
           if(Math.random()>0.96) {
             $.fn.sheep.playSound('meh');
           }
       }; // end switch(obj.type)


       $('#'+sheepObj.id).animate(anim, duration, function() {
         var toRemoveId = $(this).attr('id');
         if(sheepObj.type=='flower') {
           $(this).remove();
         } else {
           if(Math.random()<0.88) {
             $(this).parent().sheep({position:$(this).data('position'),coords:$(this).data('coords')});
             $(this).remove();
           } else {
             $(this).parent().sheep();
             $(this).animate({opacity:0}, 1500, function() {
               $(this).remove();
             });
           }
         }
         $.fn.sheep.removeId( toRemoveId );
       })
     } catch(e) {
       $('#'+sheepObj.id).attr('data-failed', 'true');
       $.fn.sheep.removeId( sheepObj.id );
     }
   };

   $.fn.sheep.getRandomSheep = function() {
     var randomSheepId = window.sheeps.index[Math.floor(Math.random()*window.sheeps.index.length)];
     if(randomSheepId=='flower') randomSheepId='sheep-eat';
     return randomSheepId;
   };

   $.fn.sheep.getUniqueId = function(randomSheepId) {
     var ret = "";
     if(!window.sheeps.sheepCount[randomSheepId]) {
       window.sheeps.sheepCount[randomSheepId] = 0;
     }
     window.sheeps.sheepCount[randomSheepId]++;
     ret = randomSheepId+"_"+window.sheeps.sheepCount[randomSheepId];
     window.sheeps.allSheepIDs.push(ret);
     return ret;
   };

   $.fn.sheep.removeId = function(sheepId) {
     for(var i=0 ; i < window.sheeps.allSheepIDs.length ; i++ ) {
       if( window.sheeps.allSheepIDs[i] == sheepId) {
         window.sheeps.allSheepIDs.splice(i, 1);
       }
     }
   }

})(jQuery);

<?
  exit(0);
}elseif(isset($_GET['view_source'])) {
  highlight_file(basename(__FILE__));
  exit(0);
}

?><!doctype html public "☠">
<html>
<head>
<title>jQuery Sheep Plugin</title>
<meta charset="UTF-8" />
<style type="text/css">

body {
  background-color:#cecece;
}

h1,p {
  text-align:center;
}

#main {
  height:500px;
  width:800px;
  background-color:#ababab;
  position:absolute;
  left:50%;
  margin-left:-400px;
}

.item {
  width:150px;
  height:75px;
  background:blue;
  border:10px solid green;
  position:absolute;
}

</style>
<script src="/js/jquery-1.8.2.js" type="text/javascript"></script>
<script src="<?=basename(__FILE__);?>/jquery.sheep.js" type="text/javascript"></script>
</head>
<body>
  <h1>jQuery Sheep generator</h1>
  <p>
    A useless jQuery plugin to spawn noisy clickable sheeps<br />
    and other animated gifs all around your boxes.<br />
    Brought to you by <a target="_blank" href="https://twitter.com/intent/follow?original_referer=http%3A%2F%2Fphpsecure.info%2Fsheep.php&region=follow_link&screen_name=TobozoTagada&tw_p=followbutton&variant=2.0">tobozo</a> (c+) 2013<br />
    <a href="<?=basename(__FILE__);?>/jquery.sheep.js">view plugin source</a>
  </p>
<div id="main">
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
</div>
<div style="margin-top:550px;text-align:center">
    <a href="https://twitter.com/share" class="twitter-share-button" data-via="TobozoTagada">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>
<div align="center">
  <div style="white-space:pre;display:inline-block;text-align:center;font-size:10px;font-family:sans-serif;">
    Credits :
<?=$credits;?>
  </div>
</div>
<script type="text/javascript">

$(document).ready(function() {
  var w,h,tw,th,x,y,offset;
  offset = $('#main').offset();
  w = $('#main').width();
  h = $('#main').height();

  $('.item').each(function() {
     // randomly positionnned boxes
     tw = $(this).width();
     th = $(this).height();
     x =  (w* Math.random()) - tw + (offset.left/2);
     y =  (h* Math.random()) + tw - offset.top;

     $(this).css({top:y,left:x});
  });

  $('.item').sheep();

})

</script>
</body>
</html><?

// returns true if animation runs less than 15 seconds
// returns false if animation loops continuously or for longer than 15 seconds
function checkGIFAnimation($image_filename)
{
  $max_animation_duration = 15; // in seconds

  // if set to loop continuously, return false
  $iterations = getGIFIterations($image_filename);
  if ($iterations == 0) {
    return false;
  }

  $delay = getGIFDuration($image_filename);

  if ($delay * $iterations > $max_animation_duration) {
    return false;
  } else {
    return true;
  }
}

function getGIFIterations($image_filename)
{
  // see http://www.w3.org/Graphics/GIF/spec-gif89a.txt and http://www.let.rug.nl/~kleiweg/gif/netscape.html for more info
  $gif_netscape_application_extension = "/21ff0b4e45545343415045322e300301([0-9a-f]{4})00/";
  $file = file_get_contents($image_filename);
  $file = bin2hex($file);

  // get looping iterations
  $iterations = 1;
  if (preg_match($gif_netscape_application_extension, $file, $matches)) {
    // convert little-endian hex unsigned int to decimal
    $iterations = hexdec(substr($matches[1],-2) . substr($matches[1], 0, 2));

    // the presence of the header with a nonzero number of iterations
    // should be interpreted as "additional" iterations,
    // hence a specifed iteration of 1 means loop twice.
    // zero iterations means loop continuously
    if ($iterations > 0) $iterations++;
  }

  // a return value of zero means gif will loop continuously
  return $iterations;
}

// returns length of time to display a gif image/animation once
// must be multipled by getGIFIterations to determine total duration
function getGIFDuration($image_filename)
{
  // see http://www.w3.org/Graphics/GIF/spec-gif89a.txt for more info
  $gif_graphic_control_extension = "/21f904[0-9a-f]{2}([0-9a-f]{4})[0-9a-f]{2}00/";
  $file = file_get_contents($image_filename);
  $file = bin2hex($file);

  // sum all frame delays
  $total_delay = 0;
  preg_match_all($gif_graphic_control_extension, $file, $matches);
  foreach ($matches[1] as $match) {
    // convert little-endian hex unsigned ints to decimals
    $delay = hexdec(substr($match,-2) . substr($match, 0, 2));
    if ($delay == 0) $delay = 1;
    $total_delay += $delay;
  }

  // delays are stored as hundredths of a second, lets convert to seconds
  $total_delay /= 100;

  return $total_delay;
}

?>