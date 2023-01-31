jQuery(document).ready(function ($) {
    var body = $('body');
    (function () {
        $('.mobile-menus .menu').hocwpMobileMenu({
            displayWidth: 980,
            position: 'right'
        });
    })();
    (function () {
        var windowWidth;
        windowWidth = $(window).width();
        $(window).resize(function () {
            windowWidth = $(window).width();
        });
        $('ul.sf-menu').superfish({
            animation: {height: 'show'},
            speed: 'medium',
            delay: 50,
            disableHI: false,
            onBeforeShow: function () {
                if (!this.is('.sf-menu > li > ul')) {
                    var subMenuWidth = $(this).width();
                    var parentLi = $(this).parent();
                    var parentWidth = parentLi.width();
                    var subMenuRight = parentLi.offset().left + parentWidth + subMenuWidth;
                    if (subMenuRight > windowWidth) {
                        $(this).css('left', 'auto');
                        $(this).css('right', parentWidth + 'px');
                    }
                }
            }
        });
    })();

    $('.hocwp .hocwp-mobile-menu .search-form .search-submit').addClass("fa").val('\uf002');
});
