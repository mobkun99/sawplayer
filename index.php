<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simplyaweeb</title>
    <link rel="stylesheet" href="./css/video-js.min.css">
    <link rel="stylesheet" href="./scss/video-skin.css">
    <link rel="stylesheet" href="./chromecast/silvermine-videojs-chromecast.css">
</head>
<body>
<video id="video-js-player" class="video-js vjs-big-play-centered"></video>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="./js/video.min.js"></script>
<script src="./js/videojs-landscape-fullscreen.min.js"></script>
<script src="./js/videojs.hotkeys.min.js"></script>
<script src="./chromecast/silvermine-videojs-chromecast.min.js"></script>
<script>

(function($){

const params = new URLSearchParams(window.location.search)

window.myPlayer = videojs("video-js-player", { 
  techOrder: ["chromecast", "html5"],
  controls: true,
  autoplay: true,
  preload: "auto",  
  playbackRates: [0.5, 1, 1.2, 1.5, 2],
  html5: {
    nativeAudioTracks: false,
    nativeVideoTracks: false, 
    hls: {
      overrideNative: true,
    }
  },
  plugins: {
    chromecast: {  
      buttonPositionIndex: 16,
    }
  },
});

// $("#video-js-player").append(
//   '<i class="material-icons rewind">fast_rewind</i><i class="material-icons fast-forward">fast_forward</i>'
// );

myPlayer.ready(function () { 
  this.hotkeys({
    volumeStep: 0.1,
    seekStep: 5,
    enableModifiersForNumbers: false,
    alwaysCaptureHotkeys: true,
    skipInitialFocus: false,
  });

  this.landscapeFullscreen({
    fullscreen: {
      enterOnRotate: true,
      alwaysInLandscapeMode: true,
      iOS: false,
    },
  });
}); 


$.ajax({ 
      type: "post",
      url: 'ajax.php',
      data: {
        id: params.get('id'),
        error : false,
      },

      success: function (response) { 
        
        console.log(response);

        if (response) {   
          let sourceStream = response;
           
          if(sourceStream.includes('m3u8')){
            simpleM3U8(sourceStream);

          } else {
            myPlayer.src({
              autoplay: true,
              type: "video/mp4",
              src: sourceStream,
            });
          }

          let streamErrorCount = 1; 

          myPlayer.on('error', () => { 
            
            console.log('error');
             
            if(streamErrorCount === 1){ 
                $.ajax({ 
                  type: "post",
                  url: 'ajax.php',
                  data: {
                    id: params.get('id'),
                    error : true,
                  },
            
                  success: function (response) {  
                      let sourceStream = response;
                        if(sourceStream.includes('m3u8')){
                          simpleM3U8(sourceStream);
              
                        } else {
                          myPlayer.src({
                            autoplay: true,
                            type: "video/mp4",
                            src: sourceStream,
                          });
                        }
                  } 
                });  

              } else { 
                streamErrorCount = 1;
              }
              
              streamErrorCount++;
           });  

          myPlayer.chromecast();   

          const fastForwardButton =  $('.fast-forward');
          const rewindButton =  $('.rewind');

          fastForwardButton.unbind('click');
          fastForwardButton.on('click', function(){
            forward(); 
          });
          
          rewindButton.unbind('click');
          rewindButton.on('click', function(){
            rewind();   
          });

        }
      }
    }); 

let seeklastTime;
let seeking = false;

function forward() {
  if (seeking && seeklastTime < myPlayer.currentTime()) {
    myPlayer.currentTime(myPlayer.currentTime() + 80);
    seeking = false;
  } else {
    setTimeout(() => {
      myPlayer.currentTime(myPlayer.currentTime() + 10);
      seeking = false;
    }, 1500);
  }

  seeking = true;
  seeklastTime = myPlayer.currentTime();
  setTimeout(() => {
    seeking = false;
  }, 500);
}

function rewind() { 
  myPlayer.currentTime(myPlayer.currentTime() - 10);
}

function simpleM3U8(sourceStream) {
  myPlayer.src({ 
    autoplay: true,
    type: 'application/x-mpegURL',
    src: sourceStream,
  }); 
}

})(jQuery); 

</script>
</html>
