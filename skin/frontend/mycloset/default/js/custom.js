
var jqCustom = jQuery.noConflict();
jqCustom(document).ready(function () {
    var winWidth=jqCustom(window).width();
    if(winWidth >=768)
    {
        jqCustom(".mainMenu .nav > li").hover(function(){

            jqCustom(this).children('ul').stop(true,true).slideDown();
        },function(){
            jqCustom(this).children('ul').slideUp();
        })
    }
    else
    {
        jqCustom(".sub-nav").each(function(){
            var thisElm=jqCustom(this).parent("li");
           jqCustom('<b aria-hidden="true" class="glyphicon subNavIcon glyphicon-chevron-down"></b>').appendTo(thisElm);
        });
        jqCustom(".mainMenu .nav > li > b").click(function(){
            jqCustom(this).parent('li').toggleClass("active");
            jqCustom(this).siblings('ul').stop(true,true).slideToggle();
        })
    }
    
    var footHeight = jqCustom("footer").outerHeight();
    jqCustom(".footFix").css({"paddingBottom": (footHeight+20) + "px"});
    jqCustom("header .loginDetails span").click(function () {
        jqCustom("header .loginDetails .links").fadeToggle(function () {
            jqCustom("header .loginDetails .search").fadeToggle();
        });
    });

    jqCustom(".small-accordion h4").click(function () {
        if (jqCustom(this).next("div").css('display') == 'none')
        {
            jqCustom(".small-accordion > div").slideUp();
            jqCustom(this).next("div").slideDown();
        }
        else
        {
            jqCustom(this).next("div").slideUp();
        }

    })

    jqCustom(".customDropDown").click(function () {
        if (jqCustom(this).children().children("ul").css('display') == 'none')
        {
            jqCustom(this).children().children("ul").slideDown();
        }

    });
   
    jqCustom(".customDropDown ul li.selected").each(function(){
        var customDropSelected = jqCustom(this).html();
        
       // console.log(customDropSelected);
        jqCustom(this).parent("ul").siblings("span").html(customDropSelected);
    });
    
    jqCustom(".customDropDown ul li").click(function () {
        jqCustom(".customDropDown ul li.selected").removeClass("selected");
        var thisHtml = jqCustom(this).html();
        jqCustom(".customDropDown ul").fadeOut(100);
        jqCustom(this).addClass("selected").parent().siblings("span").html(thisHtml);

    });
    

    jqCustom('.inputz').keypress(function (e) {
        var key = e.which;
        if (key == 13)  // the enter key code
        {
            jqCustom(".tags").append("<span>" + jqCustom('.inputz').val() + "<i class='closeppp'>X</i></span>");
            jqCustom("input.inputz").keypress(function (e) {
                if (!empty(jqCustom(this).val())) {
                    jqCustom(this).val("");
                }
            });
        }

});
      jqCustom(".closeppp").click(function () {
        jqCustom("input.inputz").remove();
    });
   jqCustom(".close, .popClose").click(function () {
            jqCustom(".popupWrapper").fadeOut();
            jqCustom(".popupBox").empty();
        });
    jqCustom(".show").click(function () {
        jqCustom(".popupWrapper").fadeIn(500);
    });
    
    jqCustom('#popupBox').bind("DOMSubtreeModified",function(){
         jqCustom('.flexsliderPopup').flexslider({
                animation: "slide",
                animationLoop: false,
                itemWidth: 150,
                itemMargin: 5
            });
      });

      jqCustom(".navbar-toggle").click(function(){
         jqCustom("body").toggleClass("showMenu") 
      });

});

jqCustom(window).resize(function () {
    var footHeight = jqCustom("footer").outerHeight();
    jqCustom(".footFix").css({"paddingBottom": footHeight + "px"})
    jqCustom('.flexsliderPopup').flexslider({
        animation: "slide",
        animationLoop: false,
        itemWidth: 150,
        itemMargin: 5
    });
    
    
    var winWidth=jqCustom(window).width();
    if(winWidth >=768)
    {
        jqCustom(".mainMenu .nav > li").hover(function(){

            jqCustom(this).children('ul').stop(true,true).slideDown();
        },function(){
            jqCustom(this).children('ul').slideUp();
        });
         jqCustom(".subNavIcon").remove();
    }
    else
    {
        jqCustom(".subNavIcon").remove();
        jqCustom(".sub-nav").each(function(){
            var thisElm=jqCustom(this).parent("li");
           jqCustom('<b aria-hidden="true" class="glyphicon glyphicon-chevron-down subNavIcon"></b>').appendTo(thisElm);
        });
        jqCustom(".mainMenu .nav > li > b").click(function(){
            jqCustom(this).parent('li').toggleClass("active");
            jqCustom(this).siblings('ul').stop(true,true).slideToggle();
        })
    }
});

jqCustom(window).load(function () {
    jqCustom('.flexslider').flexslider({
        animation: "slide",
        animationLoop: false,
        itemWidth: 300,
        itemMargin: 5
    });
});

