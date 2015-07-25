if(window.location.href.indexOf('best_import')!=-1)(function($){
    $(function(){
        
        // click on tag
        /*
        $('#bi-table .tag').mousedown(function(e){
            reg = /\&(gt|lt)\;/g;
            subtag = $(this).children('td')[0];
            tag = $(this).prevAll('.header').first().children('th')[0];
            $(':focus').val('<'+tag.innerHTML.replace(reg,'')+'/'+subtag.innerHTML.replace(reg,'')+'>');
            e.preventDefault();
        });
        */
        
        // expand subtags
        /*
        $('#bi .widefat tbody th').mousedown(function(e){
            next = $(this).parent().nextUntil('.header');
            next.toggle(300);
            e.preventDefault();
        }).css({cursor: 'pointer'}).mousedown();
        */
        
        // type change
        $('#bi-type').change(function(){
            $('#bi').submit();
        });
               
        // suggest
        $('#bi .suggest').suggest('/wp-content/plugins/best-import/admin/suggest.php', {delay:0, minchars:1});
        
        // remove field
        $('#bi input[name="removeField"]').click(function(e){
            $(this).parent().parent().remove();
            e.preventDefault();
        });
        
        // add field
        $('#bi *[id^="bi-add"]').click(function(){
            field = $(this).parent().parent().prev();
            newfield = field.clone(true).show();
            newfield.find('.suggest').suggest('/wp-content/plugins/best-import/admin/suggest.php', {delay:0, minchars:1});
            field.before(newfield);
        }).parent().parent().prev().hide();
        
        // upload progress
        bar = $('#bi .bar div');
        if(bar.length)
            $('#bi').attr('action','/wp-content/plugins/best-import/admin/upload-ajax.php').ajaxForm({
                beforeSend: function(){
                    bar.width('0%').html('0%').parent().show();
                    $('#tab-upload h4').first().html('Uploading...').removeClass('h4error');
                },
                uploadProgress: function(event, position, total, percentComplete){
                    bar.width(percentComplete+'%').html(percentComplete+'%');
                },
                success: function(){
                    bar.width('100%').html('100%');
                },
                complete: function(xhr){
                    if(xhr.responseText)$('#tab-upload h4').first().html(xhr.responseText).addClass('h4error');
                    else window.location.reload();
                }
            }); 
        
        // content tables
        $('#bi .tag-content input').click(function(e){
            current = $(this).siblings().filter('span').get(1);
            max = $(this).siblings().filter('span').get(2);
            tables = $(this).parent().next().children();
            
            n = parseInt(current.innerHTML);
            m = parseInt(max.innerHTML);

            if(this.getAttribute('name')=='next' && n<m)n++;
            else if(this.getAttribute('name')=='prev' && n>1)n--;
            
            current.innerHTML = n;
            
            tables.hide();
            $(tables.get(n-1)).show();
            
            e.preventDefault();
        });
        
        // navigation tabs
        $('.nav-tab').click(function(e){
            tab = this.getAttribute('href').slice(1);            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('[id^="tab-"]').hide();
            $('#tab-'+tab).show();
            
        }).filter(function(){
            
            hash = window.location.hash || '#home';
            return this.getAttribute('href')==hash;
            
        }).click();
        
    });
})(jQuery);