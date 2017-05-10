(function() {
    tinymce.create('tinymce.plugins.samuweb_bidu_tiny_mce', {
        init: function(ed, url) {
            ed.addButton('samuweb_bidu_tiny_mce', {
                title: 'Samuweb Bidu',
                image: url + '/samuweb-bidu-icon.png',
                onclick : function() {
                    var samuweb_bidu_selected_text = tinyMCE.activeEditor.selection.getContent();

                    // Finds out if the user didn't select anything
                    if(!samuweb_bidu_selected_text) {
                        window.alert('Feed my thrist of knowledge first!\nSelect the text that is the answer for a question');
                    } else {
                        // Asks the user for some input
                        var samuweb_bidu_prompt_title = prompt('This selected text is the answer to which question?\ne.g. How to make an awesome blog?\n(if you leave it blank, it will copy what is selected)', '');

                        // Generates the shortcode
                        if(samuweb_bidu_prompt_title != null && samuweb_bidu_prompt_title != '') {
                            ed.execCommand('mceInsertContent', false, '[bidu title="' + samuweb_bidu_prompt_title + '"]' + samuweb_bidu_selected_text + '[/bidu]');
                        } else {
                            ed.execCommand('mceInsertContent', false, '[bidu title="' + samuweb_bidu_selected_text + '"]' + samuweb_bidu_selected_text + '[/bidu]');
                        }
                    }
                }
            });
        }, createControl : function(n, cm) {
            return null;
        }, getInfo : function() {
            return {
                longname : "Samuweb Bidu",
                author : 'Sam Nasser',
                authorurl : '',
                infourl : '',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('samuweb_bidu_tiny_mce', tinymce.plugins.samuweb_bidu_tiny_mce);

})();
