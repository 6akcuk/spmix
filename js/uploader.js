/**
 * Created with JetBrains PhpStorm.
 * User: 6akcu_000
 * Date: 03.07.12
 * Time: 3:28
 * To change this template use File | Settings | File Templates.
 */

var Uploader = {
  queues: {},
  isFlashReady: false,

    defaults: {
      multi: true,
      fileFilters: [['Images', '*.jpg;*.png']],
      url: '',
      queueTpl: '<div class="filepreview clearfix">' +
        '<span class="right"></span>' +
        '<div class="left filename"></div>' +
        '<div class="left fileprogress" style="display:block"><div class="fileprogressbg"></div></div>' +
        '<div class="left filenumprogress" style="display:block"></div>' +
      '</div>',
      filenameSelector: 'div.filename',
      stateSelector: 'span.right',
      fileprogressSelector: 'div.fileprogressbg',
      fileprogressnumSelector: 'div.filenumprogress'
    },

    flashReady: function() {
      Uploader.isFlashReady = true;
      Uploader.setUploadUrl(this.options.url);
      Uploader.setFileFilters(this.options.fileFilters);
      Uploader.setURLVariables(this.options.urlVariables);
    },

    startCheck: function() {
      var upl_ready = null;
      function __upl_load() {
        if (!Uploader.isFlashReady && !domReady) {
          clearTimeout(upl_ready);
          upl_ready = setTimeout(__upl_load, 100);
        }
        else {
          clearTimeout(upl_ready);
          Uploader.setUploadUrl(A.uploadMap.action);
          Uploader.setFileFilters(Uploader.options.fileFilters);
          Uploader.setURLVariables(Uploader.options.urlVariables);
        }
      }
      upl_ready = setTimeout(__upl_load, 100);
    },

    setup: function(settings) {
      Uploader.options = $.extend(Uploader.defaults, settings);
      //Uploader.startCheck();
    },

    setUploadUrl: function(url) {
      Uploader.options.url = url;
      document.getElementById('uploader').setUploadUrl(url);
    },

    setFileFilters: function(fileFilters) {
      document.getElementById('uploader').setFileFilters(fileFilters);
    },

    setURLVariables: function(urlVariables) {
      document.getElementById('uploader').setURLVariables(urlVariables);
    },

    fileSelected: function(file, fileId) {
        var queue = $(Uploader.options.queueTpl).appendTo(Uploader.options.queue),
          queueFile = queue.find(Uploader.options.filenameSelector),
          queueState = queue.find(Uploader.options.stateSelector),
          queueProgress = queue.find(Uploader.options.fileprogressSelector),
          queueProgressNum = queue.find(Uploader.options.fileprogressnumSelector);

        queue.attr('id', 'file'+ fileId +'-queue');
        queueFile.html(file.name);

        Uploader.queues['file-'+ fileId] = {
          queue: queue,
          file: queueFile,
          state: queueState,
          progress: queueProgress,
          progressNum: queueProgressNum
        }
    },

    uploadCompleted: function(file, fileId) {
      for (var i in Uploader.queues) {
        if (i.split('-').pop() == fileId) {
          Uploader.queues[i].state.text('Фотография загружена');
        }
      }
    },

    uploadFailed: function(file, fileId) {
      for (var i in Uploader.queues) {
        if (i.split('-').pop() == fileId) {
          Uploader.queues[i].state.text('Ошибка при загрузке');
        }
      }
    },

    uploadIOError: function(file, fileId, message) {
      for (var i in Uploader.queues) {
        if (i.split('-').pop() == fileId) {
          Uploader.queues[i].state.text(message);
        }
      }
    },

    uploadProgress: function(file, fileId, bytesLoaded, bytesTotal) {
      for (var i in Uploader.queues) {
        if (i.split('-').pop() == fileId) {
          Uploader.queues[i].progress.css('width', ((bytesLoaded / bytesTotal) * 100) + '%');
          Uploader.queues[i].progressNum.text(Math.round((bytesLoaded / bytesTotal) * 100) + '%')
        }
      }
    },

    uploadHttpStatus: function(file, fileId, message) {
      for (var i in Uploader.queues) {
        if (i.split('-').pop() == fileId) {
          Uploader.queues[i].state.text('HTTP '+ message);
        }
      }
    },

    securityError: function(message) {
      boxPopup(message);
    }
};