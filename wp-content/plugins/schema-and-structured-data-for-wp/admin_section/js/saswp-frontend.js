jQuery(document).ready(function($){  

    jQuery("#saswp-comment-rating-div").rateYo({              
              rating : 1,  
              fullStar: true,                           
              onSet: function (rating, rateYoInstance) {
                $(this).next().val(rating);               
                }                              
            });             
            
});