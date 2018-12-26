import Flickity from 'flickity-hash'
import lazysizes from 'lazysizes'
import optimumx from 'lazysizes'
require('../../node_modules/lazysizes/plugins/object-fit/ls.object-fit.js')
require('../../node_modules/lazysizes/plugins/unveilhooks/ls.unveilhooks.js')
require('viewport-units-buggyfill').init();

/* globals $:false */
var width = $(window).width(),
  height = $(window).height(),
  isMobile = false,
  infos,
  target,
  lastTarget = false,
  slider,
  $mouseNav,
  $root = '';
$(function() {
  var app = {
    init: function() {
      console.log('Code by Tristan Bagot', 'www.tristanbagot.com');
      $(window).resize(function(event) {
        app.sizeSet();
      });
      $(document).ready(function($) {
        app.body = $('body');
        app.container = $('#container');
        app.sizeSet();
        app.interact();
        // app.smoothState('#main', app.container);
        $(document).keyup(function(e) {
          //esc
          if (e.keyCode === 27) app.goBack();
          if (slider && e.keyCode === 39) slider.next();
          if (slider && e.keyCode === 37) slider.previous();
        });
        $(window).on('load', function() {
          $(".loader").fadeOut(300);
        });
      });
    },
    sizeSet: function() {
      width = $(window).width();
      height = $(window).height();
      if (width <= 770)
        isMobile = true;
      if (isMobile) {
        if (width >= 770) {
          //location.reload();
          isMobile = false;
        }
      }
    },
    interact: function() {
      app.loadSlider();
      app.idle.init();
    },
    idle: {
      init: function() {
        var isProject = document.querySelector("[page-type=project]");
        if (isMobile || !isProject) return;
        var idle;
        app.body.mousemove(function(event) {
          if (!isMobile && !infos) {
            app.idle.showInfos();
            window.clearTimeout(idle);
            idle = setTimeout(function() {
              app.idle.hideInfos();
            }, 2000);
          }
        });
      },
      hideInfos: function() {
        app.body.addClass('idle');
        infos = false;
      },
      showInfos: function() {
        app.body.removeClass('idle');
        infos = true;
      },
      resetIdle: function() {
        app.showInfos();
        window.clearTimeout(idle);
        infos = false;
      }
    },
    plyr: function(loop) {
      players = plyr.setup('.js-player', {
        loop: false,
        controls: ['controls', 'progress'],
      // iconUrl: $root + "/assets/css/plyr/plyr.svg"
      });
      for (var i = players.length - 1; i >= 0; i--) {
        players[i].on('play', function(event) {
          slider.element.classList.remove('play')
          slider.element.classList.add('pause');
        });
        players[i].on('pause', function(event) {
          slider.element.classList.remove('pause')
          slider.element.classList.add('play');
        });
        players[i].on('waiting', function(event) {
          slider.element.classList.add('loading');
        });
        players[i].on('canplay', function(event) {
          slider.element.classList.remove('loading');
        });
        players[i].on('ready', function(event) {
          $(".plyr__controls").hover(function() {
            $mouseNav.css('visibility', 'hidden');
          }, function() {
            $mouseNav.css('visibility', 'visible');
          });
        });
      }
    },
    loadSlider: function() {
      $mouseNav = $('#mouse-nav');
      if (slider) {
        slider.destroy();
        slider = null;
      }
      slider = document.querySelector('.slider');
      if (slider) {
        slider = new Flickity(slider, {
          cellSelector: '.slide',
          imagesLoaded: true,
          lazyLoad: false,
          setGallerySize: false,
          accessibility: false,
          wrapAround: true,
          prevNextButtons: true,
          pageDots: false,
          autoPlay: false,
          pauseAutoPlayOnHover: false,
          draggable: isMobile
        });
        slider.slidesCount = slider.slides.length;
        if (slider.slidesCount < 1) return; // Stop if no slides
        app.mouseNav();
        // app.plyr();
        var vids = document.querySelectorAll('.slider [data-media="video"] video');
        if (vids.length > 0) {
          var hls = [];
          for (var i = vids.length - 1; i >= 0; i--) {
            vids[i].controls = false;
            if (!isMobile && vids[i].getAttribute("data-stream") && Hls.isSupported()) {
              hls[i] = new Hls({
                minAutoBitrate: 1700000
              });
              hls[i].loadSource(vids[i].getAttribute("data-stream"));
              hls[i].attachMedia(vids[i]);
              vids[i].setAttribute('poster', '');
            }
          }
        }
        slider.on('select', function() {
          // $('#slide-number').html((slider.selectedIndex + 1) + '/' + slider.slidesCount);
          $('#slide-description').html(slider.selectedElement.getAttribute("data-caption"));
          if (slider.selectedElement) slider.element.setAttribute("data-media", slider.selectedElement.getAttribute("data-media"));
          // For lazysizes
          var adjCellElems = slider.getAdjacentCellElements(2);
          $(adjCellElems).find('.lazy:not(".lazyloaded")').addClass('lazyload');
          // Update Hash
          count = slider.selectedElement.getAttribute('data-id');
          window.location.hash = count;
          slider.playPlayer();
        });
        slider.on('staticClick', function(event, pointer, cellElement, cellIndex) {
          if (!cellElement || cellElement.getAttribute('data-media') == "video" && !slider.element.classList.contains('nav-hover')) {
            return;
          } else {
            if (slider.element.classList.contains('left')) {
              slider.previous();
            } else {
              slider.next();
            }
          }
        });
        if (vids.length > 0) {
          slider.on('select', function() {
            $.each(vids, function() {
              this.pause();
            });
            slider.element.classList.remove('play', 'pause');
          });
          if (slider.selectedElement.getAttribute("data-media") == "video") {
            if (typeof hls[0] !== "undefined") {
              hls[0].on(Hls.Events.MANIFEST_PARSED, function() {
                vids[0].play();
              });
            } else {
              vids[0].play();
            }
          }
        } else if (slider.slidesCount < 2) {
          $mouseNav.remove();
          slider.element.style.cursor = 'auto';
        }
        slider.element.setAttribute("data-media", slider.selectedElement.getAttribute("data-media"));
        // Slide Hash in URL
        var hash = window.location.hash.substr(1);
        slider.selectCell('[data-id="' + hash + '"]', true, true);
        var count = slider.selectedElement.getAttribute('data-id');
        window.location.hash = count;
      }
    },
    mouseNav: function() {
      $(slider.element).mousemove(function(event) {
        if (slider) {
          var x = event.pageX;
          var y = event.pageY;
          if (x < width / 2) {
            slider.element.classList.remove('right');
            slider.element.classList.add('left');
          } else {
            slider.element.classList.remove('left');
            slider.element.classList.add('right');
          }
          if (slider.element.getAttribute("data-media") === "video" && slider.slidesCount > 1) {
            if (x < 0.15 * width || x > 0.85 * width) {
              slider.element.classList.add('nav-hover');
            } else {
              slider.element.classList.remove('nav-hover');
            }
          }
          $mouseNav.css({
            transform: "translate(" + x + "px, " + (y - $(window).scrollTop()) + "px) translateZ(0)"
          });
        }
      });
      $('#project-description, #project-title, .close-button a').hover(function() {
        $mouseNav.hide();
      }, function() {
        $mouseNav.show();
      });
    },
    goIndex: function() {},
    goBack: function() {
      if (window.history && history.length > 0 && !app.body.hasClass('projects')) {
        window.history.go(-1);
      } else {
        $('#site-title a').click();
      }
    },
    // smoothState: function(container, $target) {
    //   var options = {
    //       debug: true,
    //       scroll: false,
    //       anchors: '[data-target]',
    //       loadingClass: 'is-loading',
    //       prefetch: true,
    //       cacheLength: 4,
    //       onAction: function($currentTarget, app.container) {
    //         lastTarget = target;
    //         target = $currentTarget.data('target');
    //         if (target === "back") app.goBack();
    //       // console.log(lastTarget);
    //       },
    //       onBefore: function(request, app.container) {
    //         popstate = request.url.replace(/\/$/, '').replace(window.location.origin + $root, '');
    //       // console.log(popstate);
    //       },
    //       onStart: {
    //         duration: 0, // Duration of our animation
    //         render: function(app.container) {
    //           app.body.addClass('is-loading');
    //         }
    //       },
    //       onReady: {
    //         duration: 0,
    //         render: function(app.container, $newContent) {
    //           // Inject the new content
    //           $(window).scrollTop(0);
    //           app.body.attr('page-type', $newContent.find("#page-content").attr('page-type'));
    //           app.container.html($newContent);
    //         }
    //       },
    //       onAfter: function(app.container, $newContent) {
    //         app.interact();
    //         setTimeout(function() {
    //           app.body.removeClass('is-loading');
    //         // Clear cache for random content
    //         // smoothState.clear();
    //         }, 200);
    //       }
    //     },
    //     smoothState = $(container).smoothState(options).data('smoothState');
    // }
  };
  app.init();
});