var LayersliderInit = function () {

    return {
        initLayerSlider: function () {
            $('#layerslider').layerSlider({
                skinsPath : '../img/skins/',
                skin : 'fullwidth',
                thumbnailNavigation : 'hover',
                hoverPrevNext : false,
                responsive : false,
                responsiveUnder : 960,
                layersContainer : 960
            });
        }
    };

}();