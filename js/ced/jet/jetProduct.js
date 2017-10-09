/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

var id = '';
function uploadProduct(e,product_id,url){
    
    var skin_path = url.split('/index.php');
   var cont_path = url;
    //e.preventDefault();

    $('loading-mask').setStyle({zIndex: '-500'});
    /*jQuery('#savereal_'+newtext).remove(); */
    //console.log($('manage_'+product_id).select('img'));

    id = product_id;

    $('upload_'+product_id).hide();

    var loader = 'loader_'+product_id;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '50%' , 'height' : '50%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('manage_'+product_id).insert(content);
    var post = [product_id];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/directapiupload',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  entity_id: product_id },
            onSuccess: function(transport){

                var json = [];
                try {
                    json =  JSON.parse(transport.responseText);
                } catch(error) {
                    alert("Some thing bad happened! Please try again.");
                    $(loader).remove();
                    return;
                }



                if(json.error){
                  err_msg = json.error;
                    $(loader).remove();
                    $('manage_'+product_id).insert(  '<a id="error_'+product_id+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_id+')\'>Error</a>' );
                    
                }else{
                    $(loader).remove();
                    $('manage_'+product_id).insert(  '<span style="color : green" id ="success_'+product_id+'">success</span>' );
                }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}
function archieveProduct(e,product_sku,url){


    var skin_path = url.split('/index.php');
   var cont_path = url;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
    $('archieve_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '20%' , 'height' : '2+0%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('manage_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/livearchieve',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('manage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 

                    $(loader).remove();
                    $('manage_'+product_sku).insert(  '<span style="color : green" id ="success_'+product_sku+'">success</span>' );
                }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}
/* code start for get price*/
function priceProduct(e,product_sku,url){


    var skin_path = url.split('/index.php');
   var cont_path = url;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
    $('price_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '20%' , 'height' : '2+0%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('pmanage_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/getprice',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('pmanage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 
                    success_msg = transport.responseText.evalJSON(true).success;
                    
                    $(loader).remove();
                    $('pmanage_'+product_sku).insert(  '<span style="color : blue" id ="success_'+product_sku+'">'+success_msg+'</span>' );
                }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}
/* code end for get price*/
/* code start for get qty*/
function qtyProduct(e,product_sku,url){


    var skin_path = url.split('/index.php');
   var cont_path = url;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
    $('qty_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '20%' , 'height' : '2+0%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('qmanage_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/getqty',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('qmanage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 
                    success_msg = transport.responseText.evalJSON(true).success;
                    
                    $(loader).remove();
                    $('qmanage_'+product_sku).insert(  '<span style="color : blue" id ="success_'+product_sku+'">'+success_msg+'</span>' );
                }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}
/* code end for get price*/
/* code start for get sales data*/
function analysisProduct(e,product_sku,url){


    var skin_path = url.split('/index.php');
   var cont_path = url;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
    $('analysis_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '20%' , 'height' : '2+0%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('amanage_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/getsalesdata',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('amanage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 
                    message = transport.responseText.evalJSON(true).message;
                    
                    
                    $(loader).remove();
                   
                   $('amanage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox0("error" , "'+message+'","'+product_sku+'")\'>Show</a>' );
                  
                }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}
/* code end for get price*/
function unarchieveProduct(e,product_sku,url){
    
    var skin_path = url.split('/index.php');
   var cont_path = url;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
    $('unarchieve_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 1px ; padding-top : 1px' , 'width' : '20%' , 'height' : '20%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('managearch_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/liveunarchieve',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('managearch_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 

                    $(loader).remove();
                    $('managearch_'+product_sku).insert(  '<span style="color : green" id ="success_'+product_sku+'">success</span>' );
                }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}
function dialogBox(type , msg , product_id) {
    if(type == 'error'){
            jetPopup = new Window({
                id:"popup_window",
                className: "magento",
                windowClassName: "popup-window",
                title: "Error Uploading Product !! ",
                draggable:true,
                wiredDrag: true,
                width: 300,
                height: 126,
                minimizable: false,
                maximizable: false,
                showEffectOptions: {
                    duration: 0.4
                },
                hideEffectOptions:{
                    duration: 0.4
                },
                destroyOnClose: true,
                OnClose : confirmExit
            });
        jetPopup.getContent().innerHTML='<span style="color: #df280a">'+msg+'</<style>';
        jetPopup.setZIndex(100);
        jetPopup.showCenter(true);
    }
    Event.observe($('popup_window_close'), 'click', confirmExit);
}
function dialogBox0(type , msg , product_id) {
    if(type == 'error'){
            jetPopup = new Window({
                id:"popup_window",
                className: "magento",
                windowClassName: "popup-window",
                title: "Competitors Analysis ",
                draggable:true,
                wiredDrag: true,
                width: 300,
                height: 126,
                minimizable: false,
                maximizable: false,
                showEffectOptions: {
                    duration: 0.4
                },
                hideEffectOptions:{
                    duration: 0.4
                },
                destroyOnClose: true,
                OnClose : confirmExit
            });
        jetPopup.getContent().innerHTML='<span style="color: #df280a">'+msg+'</<style>';
        jetPopup.setZIndex(100);
        jetPopup.showCenter(true);
    }
    Event.observe($('popup_window_close'), 'click', confirmExit0(product_id));
}


    function confirmExit(){
       
          $('error_'+id).remove();
        $('upload_'+id).setStyle({display : 'block'});  
        
    }
    function confirmExit0(id){
       
          $('error_'+id).remove();
        $('analysis_'+id).setStyle({display : 'block'});  
        
    }
   
    function saveTrigger(url){


         var data=document.getElementsByClassName("saveall");
        var ids=new Array();
        for (i = 0; i < data.length; i++){
            var arr=data[i]['id'].split('_');
           ids.push(arr[1]);
           
        }
        //var new_idss = ids+'';
        //var id = new Array();
        //id = ids.toString().split(',');
             
     //for(var zz = 0;zz<ids.length;zz++)
       //{
         //    manageproducts_saveallnext(id[zz]);
       //}
        
        manageproducts_saveallnext(url);
        function manageproducts_saveallnext(url) {
             //var new_idss = ids+'';
        //var id = new Array();
        //id = ids.toString().split(',');
        //for(var zz = 0;zz<ids.length;zz++)
       //{
         //    manageproducts_saveallnext(id[zz]);
       //}
             var iddd = ids.shift();
                if(iddd)
                   uploadoneProduct('event',iddd,true,url);
        }

        function uploadoneProduct(e,product_id,saveall,url){
    
            var skin_path = url.split('/index.php');
            var cont_path = url;
            
    //e.preventDefault();

    $('loading-mask').setStyle({zIndex: '-500'});
    /*jQuery('#savereal_'+newtext).remove(); */
    //console.log($('manage_'+product_id).select('img'));

    id = product_id;

    $('upload_'+product_id).hide();

    var loader = 'loader_'+product_id;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '50%' , 'height' : '50%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    if (document.contains(document.getElementById('error_'+product_id))) {
        $('error_'+product_id).remove();
    }
     if (document.contains(document.getElementById('success_'+product_id))) {
         $('success_'+product_id).remove();
    }
   
    $('manage_'+product_id).insert(content);
    var post = [product_id];
    new Ajax.Request(cont_path+'adminhtml_jetproduct/directapiupload',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  entity_id: product_id },
            onSuccess: function(transport){

                    
                    
                 
                if(transport.responseText.evalJSON(true).error){ 
                    
                    
                    err_msg = transport.responseText.evalJSON(true).error;


                    $(loader).remove();
                   
                    $('manage_'+product_id).insert(  '<a id="error_'+product_id+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_id+')\'>Error</a>' );
                   
                }else{ 
                    $(loader).remove();
                    $('manage_'+product_id).insert(  '<span style="color : green" id ="success_'+product_id+'">success</span>' );
                }
                 if(saveall){
                            manageproducts_saveallnext(url);
                        }


            },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}

    } 

    /*code start for archieve all and unarchieve all*/
function archieveAll(url){


        
         var data=document.getElementsByClassName("archieveall");
        var ids=new Array();
        for (i = 0; i < data.length; i++){
            var arr=data[i]['id'].split('_');
           ids.push(arr[1]);
           
        }
        //var new_idss = ids+'';
        //var id = new Array();
        //id = ids.toString().split(',');
             
     //for(var zz = 0;zz<ids.length;zz++)
       //{
         //    manageproducts_archieveallnext(id[zz]);
       //}
        
        manageproducts_archieveallnext(url);
        function manageproducts_archieveallnext(url) {
             //var new_idss = ids+'';
        //var id = new Array();
        //id = ids.toString().split(',');
        //for(var zz = 0;zz<ids.length;zz++)
       //{
         //    manageproducts_archieveallnext(id[zz]);
       //}
             var iddd = ids.shift();
                if(iddd)
                   archieveoneProduct('event',iddd,true,url);
        }

        function archieveoneProduct(e,product_id,saveall,url){
    
            var skin_path = url.split('/index.php');
   var cont_path = url;
   product_sku = product_id;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
    $('archieve_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '20%' , 'height' : '2+0%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('manage_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/livearchieve',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){


                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('manage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 

                    $(loader).remove();
                    $('manage_'+product_sku).insert(  '<span style="color : green" id ="success_'+product_sku+'">success</span>' );
                }
                 if(saveall){
                            manageproducts_archieveallnext(url);
                        }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}

    } 
    /* code end */

    /*code start for archieve all and unarchieve all*/
function unarchieveAll(url){


        
         var data=document.getElementsByClassName("unarchieveall");
        var ids=new Array();
        for (i = 0; i < data.length; i++){
            var arr=data[i]['id'].split('_');
           ids.push(arr[1]);
           
        }
        //var new_idss = ids+'';
        //var id = new Array();
        //id = ids.toString().split(',');
             
     //for(var zz = 0;zz<ids.length;zz++)
       //{
         //    manageproducts_archieveallnext(id[zz]);
       //}
        
        manageproducts_unarchieveallnext(url);
        function manageproducts_unarchieveallnext(url) {
             //var new_idss = ids+'';
        //var id = new Array();
        //id = ids.toString().split(',');
        //for(var zz = 0;zz<ids.length;zz++)
       //{
         //    manageproducts_archieveallnext(id[zz]);
       //}
             var iddd = ids.shift();
                if(iddd)
                   unarchieveoneProduct('event',iddd,true,url);
        }

        function unarchieveoneProduct(e,product_id,saveall,url){
    
            var skin_path = url.split('/index.php');
   var cont_path = url;
   product_sku = product_id;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
    $('unarchieve_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 1px ; padding-top : 1px' , 'width' : '20%' , 'height' : '20%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('managearch_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/liveunarchieve',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('managearch_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 

                    $(loader).remove();
                    $('managearch_'+product_sku).insert(  '<span style="color : green" id ="success_'+product_sku+'">success</span>' );
                }
                 if(saveall){
                            manageproducts_unarchieveallnext(url);
                        }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}

    } 
    /* code end */
    /*code start for get all price*/
function priceAll(url){
        var data=document.getElementsByClassName("priceall");
        var ids=new Array();
        for (i = 0; i < data.length; i++){
            var arr=data[i]['id'].split('_');
           ids.push(arr[1]);
           
        }
        manageproducts_priceeallnext(url);
        function manageproducts_priceeallnext(url) {
           
             var iddd = ids.shift();
                if(iddd)
                   priceoneProduct('event',iddd,true,url);
        }

        function priceoneProduct(e,product_id,saveall,url){
    
            var skin_path = url.split('/index.php');
   var cont_path = url;
   product_sku = product_id;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
   $('price_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '20%' , 'height' : '2+0%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('pmanage_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/getprice',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('pmanage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 
                    success_msg = transport.responseText.evalJSON(true).success;
                    
                    $(loader).remove();
                    $('pmanage_'+product_sku).insert(  '<span style="color : blue" id ="success_'+product_sku+'">'+success_msg+'</span>' );
                }
                 if(saveall){
                            manageproducts_priceeallnext(url);
                        }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}

    } 
    /* code end */
    /*code start for get all qty*/
function qtyAll(url){
        var data=document.getElementsByClassName("qtyall");
        var ids=new Array();
        for (i = 0; i < data.length; i++){
            var arr=data[i]['id'].split('_');
           ids.push(arr[1]);
           
        }
        manageproducts_qtyeallnext(url);
        function manageproducts_qtyeallnext(url) {
           
             var iddd = ids.shift();
                if(iddd)
                   qtyoneProduct('event',iddd,true,url);
        }

        function qtyoneProduct(e,product_id,saveall,url){
    
            var skin_path = url.split('/index.php');
   var cont_path = url;
   product_sku = product_id;
   $('loading-mask').setStyle({zIndex: '-500'});
   sku = product_sku;
   $('qty_'+product_sku).hide();
    var loader = 'loader_'+product_sku;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '20%' , 'height' : '2+0%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('qmanage_'+product_sku).insert(content);
    var post = [product_sku];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/getqty',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  sku: product_sku },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('qmanage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );
                    
                }else{ 
                    success_msg = transport.responseText.evalJSON(true).success;
                    
                    $(loader).remove();
                    $('qmanage_'+product_sku).insert(  '<span style="color : blue" id ="success_'+product_sku+'">'+success_msg+'</span>' );
                }
                if(saveall){
                            manageproducts_qtyeallnext(url);
                        }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}

    } 
    /* code end */
    /*code start for get all analysis*/
function analysisAll(url){
        var data=document.getElementsByClassName("analysisall");
        var ids=new Array();

        for (i = 0; i < data.length; i++){
            var arr=data[i]['id'].split('_');
           ids.push(arr[1]);
        }

        manageproducts_analysisallnext(url);
        function manageproducts_analysisallnext(url) {
           
             var iddd = ids.shift();
                if(iddd)
                   analysisoneProduct('event',iddd,true,url);
        }

        function analysisoneProduct(e,product_id,saveall,url){
    
            var skin_path = url.split('/index.php');
           var cont_path = url;
           product_sku = product_id;
           $('loading-mask').setStyle({zIndex: '-500'});
           sku = product_sku;
           $('analysis_'+product_sku).hide();
            var loader = 'loader_'+product_sku;
            var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '20%' , 'height' : '2+0%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
            $('amanage_'+product_sku).insert(content);
            var post = [product_sku];
           new Ajax.Request(cont_path+'adminhtml_jetproduct/getsalesdata',
                {
                    method:'post',
                    parameters: { form_key: FORM_KEY,  sku: product_sku },
                    onSuccess: function(transport){

                        if(transport.responseText.evalJSON(true).error){

                          err_msg = transport.responseText.evalJSON(true).error;
                            $(loader).remove();
                            $('amanage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_sku+')\'>Error</a>' );

                        }else{
                            message = transport.responseText.evalJSON(true).message;


                            $(loader).remove();

                           $('amanage_'+product_sku).insert(  '<a id="error_'+product_sku+'" href="javascript: void(0);" onclick = \'dialogBox0("error" , "'+message+'","'+product_sku+'")\'>Show</a>' );

                        }
                        if(saveall){
                                    manageproducts_analysisallnext(url);
                                }

                        },

                    onFailure: function(){
                      alert('Error');
                      $(loader).remove();
                        }
                });


        }

}
    /* code end */

