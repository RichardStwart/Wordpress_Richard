!function(){var a=tinymce.DOM;tinymce.create("tinymce.plugins.WordPress",{init:function(b,c){var d,e,f,g,h,i=this,j=b.getParam("wordpress_adv_toolbar","toolbar2"),k=0;d='<img src="'+c+'/img/trans.gif" class="mce-wp-more mceItemNoResize" title="'+b.getLang("wordpress.wp_more_alt")+'" />',e='<img src="'+c+'/img/trans.gif" class="mce-wp-nextpage mceItemNoResize" title="'+b.getLang("wordpress.wp_page_alt")+'" />',"1"==getUserSetting("hidetb","0")&&(b.settings.wordpress_adv_hidden=0),b.onPostRender.add(function(){var c=b.controlManager.get(j);b.getParam("wordpress_adv_hidden",1)&&c&&(a.hide(c.id),i._resizeIframe(b,j,28))}),b.addCommand("WP_More",function(){b.execCommand("mceInsertContent",0,d)}),b.addCommand("WP_Page",function(){b.execCommand("mceInsertContent",0,e)}),b.addCommand("WP_Help",function(){b.windowManager.open({url:tinymce.baseURL+"/wp-mce-help.php",width:450,height:420,inline:1})}),b.addCommand("WP_Adv",function(){var c=b.controlManager,d=c.get(j).id;"undefined"!=d&&(a.isHidden(d)?(c.setActive("wp_adv",1),a.show(d),i._resizeIframe(b,j,-28),b.settings.wordpress_adv_hidden=0,setUserSetting("hidetb","1")):(c.setActive("wp_adv",0),a.hide(d),i._resizeIframe(b,j,28),b.settings.wordpress_adv_hidden=1,setUserSetting("hidetb","0")))}),b.addCommand("WP_Medialib",function(){"undefined"!=typeof wp&&wp.media&&wp.media.editor&&wp.media.editor.open(b.id)}),b.addButton("wp_more",{title:"wordpress.wp_more_desc",cmd:"WP_More"}),b.addButton("wp_page",{title:"wordpress.wp_page_desc",image:c+"/img/page.gif",cmd:"WP_Page"}),b.addButton("wp_help",{title:"wordpress.wp_help_desc",cmd:"WP_Help"}),b.addButton("wp_adv",{title:"wordpress.wp_adv_desc",cmd:"WP_Adv"}),b.addButton("add_media",{title:"wordpress.add_media",image:c+"/img/image.gif",cmd:"WP_Medialib"}),b.onBeforeExecCommand.add(function(a,b,c,d,e){var f,g,i,j,k,l,m=tinymce.DOM;"mceFullScreen"==b&&"mce_fullscreen"!=a.id&&m.select("a.thickbox").length&&(a.settings.theme_advanced_buttons1+=",|,add_media"),("JustifyLeft"==b||"JustifyRight"==b||"JustifyCenter"==b)&&(f=a.selection.getNode(),"IMG"==f.nodeName&&(l=b.substr(7).toLowerCase(),k="align"+l,g=a.dom.getParent(f,"dl.wp-caption"),i=a.dom.getParent(f,"div.mceTemp"),g&&i?(j=a.dom.hasClass(g,k)?"alignnone":k,g.className=g.className.replace(/align[^ '"]+\s?/g,""),a.dom.addClass(g,j),"aligncenter"==j?a.dom.addClass(i,"mceIEcenter"):a.dom.removeClass(i,"mceIEcenter"),e.terminate=!0,a.execCommand("mceRepaint")):a.dom.hasClass(f,k)?a.dom.addClass(f,"alignnone"):a.dom.removeClass(f,"alignnone"))),!tinymce.isWebKit||"InsertUnorderedList"!=b&&"InsertOrderedList"!=b||(h||(h=a.dom.create("style",{type:"text/css"},"#tinymce,#tinymce span,#tinymce li,#tinymce li>span,#tinymce p,#tinymce p>span{font:medium sans-serif;color:#000;line-height:normal;}")),a.getDoc().head.appendChild(h))}),b.onExecCommand.add(function(a,b){tinymce.isWebKit&&h&&("InsertUnorderedList"==b||"InsertOrderedList"==b)&&a.dom.remove(h)}),b.onInit.add(function(a){var b=a.getParam("body_class",""),c=a.getBody();b=b?b.split(" "):[],"rtl"==a.getParam("directionality","")&&b.push("rtl"),tinymce.isIE9?b.push("ie9"):tinymce.isIE8?b.push("ie8"):tinymce.isIE7&&b.push("ie7"),"wp_mce_fullscreen"!=a.id&&"mce_fullscreen"!=a.id?b.push("wp-editor"):"mce_fullscreen"==a.id&&b.push("mce-fullscreen"),tinymce.each(b,function(b){b&&a.dom.addClass(c,b)}),a.onNodeChange.add(function(a,b,c){var d;"IMG"==c.nodeName?d=a.dom.getParent(c,"dl.wp-caption"):"DIV"==c.nodeName&&a.dom.hasClass(c,"mceTemp")&&(d=c.firstChild,a.dom.hasClass(d,"wp-caption")||(d=!1)),d&&(a.dom.hasClass(d,"alignleft")?b.setActive("justifyleft",1):a.dom.hasClass(d,"alignright")?b.setActive("justifyright",1):a.dom.hasClass(d,"aligncenter")&&b.setActive("justifycenter",1))}),a.onBeforeSetContent.add(function(a,b){b.content&&(b.content=b.content.replace(/<p>\s*<(p|div|ul|ol|dl|table|blockquote|h[1-6]|fieldset|pre|address)( [^>]*)?>/gi,"<$1$2>"),b.content=b.content.replace(/<\/(p|div|ul|ol|dl|table|blockquote|h[1-6]|fieldset|pre|address)>\s*<\/p>/gi,"</$1>"))})}),"undefined"!=typeof jQuery&&b.onKeyUp.add(function(a,b){var c=b.keyCode||b.charCode;c!=k&&((13==c||8==k||46==k)&&jQuery(document).triggerHandler("wpcountwords",[a.getContent({format:"raw"})]),k=c)}),b.onSaveContent.addToTop(function(a,b){b.content=b.content.replace(/<p>(<br ?\/?>|\u00a0|\uFEFF)?<\/p>/g,"<p>&nbsp;</p>")}),b.onSaveContent.add(function(a,b){a.isHidden()?b.content=b.element.value:a.getParam("wpautop",!0)&&"object"==typeof switchEditors&&(b.content=switchEditors.pre_wpautop(b.content))}),i._handleMoreBreak(b,c),g="alt+shift",b.addShortcut(g+"+c","justifycenter_desc","JustifyCenter"),b.addShortcut(g+"+r","justifyright_desc","JustifyRight"),b.addShortcut(g+"+l","justifyleft_desc","JustifyLeft"),b.addShortcut(g+"+j","justifyfull_desc","JustifyFull"),b.addShortcut(g+"+q","blockquote_desc","mceBlockQuote"),b.addShortcut(g+"+u","bullist_desc","InsertUnorderedList"),b.addShortcut(g+"+o","numlist_desc","InsertOrderedList"),b.addShortcut(g+"+n","spellchecker.desc","mceSpellCheck"),b.addShortcut(g+"+a","link_desc","WP_Link"),b.addShortcut(g+"+s","unlink_desc","unlink"),b.addShortcut(g+"+m","image_desc","WP_Medialib"),b.addShortcut(g+"+z","wordpress.wp_adv_desc","WP_Adv"),b.addShortcut(g+"+t","wordpress.wp_more_desc","WP_More"),b.addShortcut(g+"+d","striketrough_desc","Strikethrough"),b.addShortcut(g+"+h","help_desc","WP_Help"),b.addShortcut(g+"+p","wordpress.wp_page_desc","WP_Page"),b.addShortcut("ctrl+s","save_desc",function(){"function"==typeof autosave&&autosave()}),/\bwpfullscreen\b/.test(b.settings.plugins)?b.addShortcut(g+"+w","wordpress.wp_fullscreen_desc","wpFullScreen"):/\bfullscreen\b/.test(b.settings.plugins)&&b.addShortcut(g+"+g","fullscreen.desc","mceFullScreen"),b.onInit.add(function(a){tinymce.dom.Event.add(a.getWin(),"scroll",function(){a.plugins.wordpress._hideButtons()}),tinymce.dom.Event.add(a.getBody(),"dragstart",function(){a.plugins.wordpress._hideButtons()})}),b.onBeforeExecCommand.add(function(a){a.plugins.wordpress._hideButtons()}),b.onSaveContent.add(function(a){a.plugins.wordpress._hideButtons()}),b.onMouseDown.add(function(a,b){"IMG"!=b.target.nodeName&&a.plugins.wordpress._hideButtons()}),b.onKeyDown.add(function(a,b){(b.which==tinymce.VK.DELETE||b.which==tinymce.VK.BACKSPACE)&&a.plugins.wordpress._hideButtons()}),f=function(a){var c;if("mceModalBlocker"==a.target.id||"ui-widget-overlay"==a.target.className)for(c in b.windowManager.windows)b.windowManager.close(null,c)},tinymce.dom.Event.remove(document.body,"click",f),tinymce.dom.Event.add(document.body,"click",f)},getInfo:function(){return{longname:"WordPress Plugin",author:"WordPress",authorurl:"http://wordpress.org",infourl:"http://wordpress.org",version:"3.0"}},_setEmbed:function(a){return a.replace(/\[embed\]([\s\S]+?)\[\/embed\][\s\u00a0]*/g,function(a,b){return'<img width="300" height="200" src="'+tinymce.baseURL+'/plugins/wordpress/img/trans.gif" class="wp-oembed mceItemNoResize" alt="'+b+'" title="'+b+'" />'})},_getEmbed:function(a){return a.replace(/<img[^>]+>/g,function(a){if(-1!=a.indexOf('class="wp-oembed')){var b=a.match(/alt="([^\"]+)"/);b[1]&&(a="[embed]"+b[1]+"[/embed]")}return a})},_showButtons:function(a,b){var c,d,e,f,g,h=tinyMCE.activeEditor,i=tinymce.DOM;e=h.dom.getViewPort(h.getWin()),c=i.getPos(h.getContentAreaContainer()),d=h.dom.getPos(a),f=Math.max(d.x-e.x,0)+c.x,g=Math.max(d.y-e.y,0)+c.y,i.setStyles(b,{top:g+5+"px",left:f+5+"px",display:"block"})},_hideButtons:function(){var a=tinymce.DOM;a.hide(a.select("#wp_editbtns, #wp_gallerybtns"))},_resizeIframe:function(b,c,d){var e=b.getContentAreaContainer().firstChild;a.setStyle(e,"height",e.clientHeight+d),b.theme.deltaHeight+=d},_handleMoreBreak:function(a,b){var c,d;c='<img src="'+b+'/img/trans.gif" alt="$1" class="mce-wp-more mceItemNoResize" title="'+a.getLang("wordpress.wp_more_alt")+'" />',d='<img src="'+b+'/img/trans.gif" class="mce-wp-nextpage mceItemNoResize" title="'+a.getLang("wordpress.wp_page_alt")+'" />',a.onPostRender.add(function(){a.theme.onResolveName&&a.theme.onResolveName.add(function(b,c){"IMG"==c.node.nodeName&&(a.dom.hasClass(c.node,"mce-wp-more")&&(c.name="wpmore"),a.dom.hasClass(c.node,"mce-wp-nextpage")&&(c.name="wppage"))})}),a.onBeforeSetContent.add(function(a,b){b.content&&(b.content=b.content.replace(/<!--more(.*?)-->/g,c),b.content=b.content.replace(/<!--nextpage-->/g,d))}),a.onPostProcess.add(function(a,b){b.get&&(b.content=b.content.replace(/<img[^>]+>/g,function(a){if(-1!==a.indexOf('class="mce-wp-more')){var b,c=(b=a.match(/alt="(.*?)"/))?b[1]:"";a="<!--more"+c+"-->"}return-1!==a.indexOf('class="mce-wp-nextpage')&&(a="<!--nextpage-->"),a}))}),a.onNodeChange.add(function(a,b,c){b.setActive("wp_page","IMG"===c.nodeName&&a.dom.hasClass(c,"mce-wp-nextpage")),b.setActive("wp_more","IMG"===c.nodeName&&a.dom.hasClass(c,"mce-wp-more"))})}}),tinymce.PluginManager.add("wordpress",tinymce.plugins.WordPress)}();