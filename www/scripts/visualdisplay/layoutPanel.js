var LayoutPanel = function() {
    var self = this,
        htmlPanel = '<div class="visual-diplay-layout panel block child-block" panelId="%panelId%">\
                        <div class="couter-layout-eye-btn"><i class="icon-eye-open"></i></div>\
                        <div class="couter-layout-delete-btn delete-panel"><i class="icon-trash"></i></div>\
                        <div class="content" style="padding: 0"></div>\
                        <div class="title">Panel</div>\
                    </div>',
        htmlImage = '<div class="visual-diplay-layout image block child-block" imageId="%imageId%">\
                        <div class="couter-layout-eye-btn"><i class="icon-eye-open"></i></div>\
                        <div class="couter-layout-delete-btn delete-image"><i class="icon-trash"></i></div>\
                        <div class="content" style="padding: 0"><img src="%path%"/></div>\
                        <div class="title">Image</div>\
                    </div>',
        htmlCounter = '<div class="visual-diplay-layout counter block child-block" counterId="%counterId%">\
                           <div class="couter-layout-eye-btn"><i class="icon-eye-open"></i></div>\
                           <div class="couter-layout-delete-btn delete-counter"><i class="icon-trash"></i></div>\
                           <div class="content" style="padding: 0">\
                               <div class="icon"></div>\
                               <div class="name">%counterName%</div>\
                           </div>\
                           <div class="title">Counter</div>\
                       </div>',
        $container = $('#visualDisplayLayoutContianer');
    
    this.AddPanel = function(panelId) {
        if($container == null || panelId == null) return;
        
        $container.append(htmlPanel.replace('%panelId%', panelId));
    } 
    
    this.AddImage = function(imageId, path) {
        if($container == null || imageId == null) return;
        
        $container.append(htmlImage.replace('%imageId%', imageId)
                                   .replace('%path%', path));
    }
    
    this.AddCounter = function(counterId, counterName) {
        if($container == null || counterId == null) return;
        
        $container.append(htmlCounter.replace('%counterId%', counterId)
                                     .replace('%counterName%', counterName));
    }
}

var lastTab = window.location.hash,
    currentTab = null;
if (lastTab != '') {
    $('ul.nav-tabs').children().removeClass('active');
    $('a[href='+ lastTab +']').parents('li:first').addClass('active');
    $('div.tab-content').children().removeClass('active');
    $(lastTab).addClass('active');
    currentTab = lastTab;
}

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    currentTab = $(e.target).attr('href');
});
