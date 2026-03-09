/**
 * TinyMCE Editor Configuration
 * Reusable initialization function for document editor
 */

function initializeTinyMCE(selector = '#editor', options = {}) {
    const defaultConfig = {
        selector: selector,
        height: options.height || 500,
        menubar: true,
        toolbar_location: 'top',
        plugins: [
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists',
            'media', 'searchreplace', 'table', 'visualblocks', 'wordcount', 'code',
            'fullscreen', 'insertdatetime', 'preview', 'help'
        ],
        toolbar: 'undo redo | formatpainter | blocks fontfamily fontsize | bold italic underline strikethrough | ' +
            'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | ' +
            'forecolor backcolor | link image media table | code fullscreen | removeformat help',
        font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 28pt 32pt 36pt 48pt 72pt',
        font_family_formats: 'Arial=arial,helvetica,sans-serif; ' +
            'Times New Roman=times new roman,times,serif; ' +
            'Courier New=courier new,courier,monospace; ' +
            'Georgia=georgia,serif; ' +
            'Verdana=verdana,sans-serif; ' +
            'Tahoma=tahoma,sans-serif; ' +
            'Comic Sans MS=comic sans ms,cursive; ' +
            'Impact=impact,sans-serif;' +
            'Cambria=cambria,serif',
        content_style: 'body { font-family: Cambria, serif; font-size: 12pt; }',
        automatic_uploads: true,
        file_picker_types: 'image',
        
        // Force line breaks instead of paragraphs on Enter
        // forced_root_block: false,
        force_br_newlines: true,
        // force_p_newlines: false,
        
        // Enable Tab key for indentation
        indent_use_margin: true,
        indentation: '20px',
        
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype === 'image') {
                let input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.onchange = function () {
                    let file = this.files[0];
                    if (!file) return;
                    
                    let formData = new FormData();
                    formData.append('upload', file);

                    // Get base URL from the page
                    let baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/').replace(/\/documents.*/, '/documents');
                    
                    fetch(baseUrl + '/upload-image', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Upload failed');
                            }
                            return response.json();
                        })
                        .then(result => {
                            if (result.location) {
                                callback(result.location, { alt: file.name });
                            } else {
                                alert(result.error || 'Upload failed');
                            }
                        })
                        .catch(error => {
                            console.error('Upload error:', error);
                            alert('Upload failed: ' + error.message);
                        });
                };

                input.click();
            }
        },
        images_upload_handler: function (blobInfo, success, failure) {
            let formData = new FormData();
            formData.append('upload', blobInfo.blob(), blobInfo.filename());

            // Get base URL from the page
            let baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/').replace(/\/documents.*/, '/documents');

            return new Promise((resolve, reject) => {
                fetch(baseUrl + '/upload-image', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Upload failed with status: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(result => {
                        if (result.location) {
                            resolve(result.location);
                        } else {
                            reject(result.error || 'Upload failed');
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        reject('Upload failed: ' + error.message);
                    });
            });
        },
        setup: function (editor) {
            // Custom Tab key handler for indentation
            editor.on('keydown', function(e) {
                if (e.keyCode === 9) { // Tab key
                    e.preventDefault();
                    
                    const selection = editor.selection;
                    const range = selection.getRng();
                    const selectedContent = selection.getContent();
                    
                    // Check if there's a multi-line selection
                    if (selectedContent && selectedContent.length > 0) {
                        // Text is selected - indent/outdent the selection
                        if (e.shiftKey) {
                            editor.execCommand('Outdent');
                        } else {
                            editor.execCommand('Indent');
                        }
                    } else {
                        // No selection - just cursor position
                        // Get the current position and find the line boundaries
                        const node = range.startContainer;
                        const offset = range.startOffset;
                        
                        // Get the parent element
                        let parentElement = node.nodeType === 3 ? node.parentNode : node;
                        
                        // Find line boundaries (text between <br> tags or start/end of element)
                        let lineStart = null;
                        let lineEnd = null;
                        
                        // Walk backwards to find line start (previous <br> or element start)
                        let walker = node;
                        let currentOffset = offset;
                        
                        while (walker) {
                            if (walker.nodeType === 1 && walker.tagName === 'BR') {
                                lineStart = walker;
                                break;
                            }
                            
                            let prev = walker.previousSibling;
                            if (!prev) {
                                walker = walker.parentNode;
                                if (walker === editor.getBody() || !walker) {
                                    lineStart = null; // Start of content
                                    break;
                                }
                            } else {
                                walker = prev;
                                if (walker.nodeType === 1 && walker.tagName === 'BR') {
                                    lineStart = walker;
                                    break;
                                }
                                // Continue walking
                                while (walker.lastChild) {
                                    walker = walker.lastChild;
                                }
                            }
                        }
                        
                        // Walk forwards to find line end (next <br> or element end)
                        walker = node;
                        while (walker) {
                            if (walker.nodeType === 1 && walker.tagName === 'BR') {
                                lineEnd = walker;
                                break;
                            }
                            
                            let next = walker.nextSibling;
                            if (!next) {
                                walker = walker.parentNode;
                                if (walker === editor.getBody() || !walker) {
                                    lineEnd = null; // End of content
                                    break;
                                }
                            } else {
                                walker = next;
                                if (walker.nodeType === 1 && walker.tagName === 'BR') {
                                    lineEnd = walker;
                                    break;
                                }
                                // Continue walking
                                while (walker.firstChild) {
                                    walker = walker.firstChild;
                                }
                            }
                        }
                        
                        // Now wrap the current line in a span with margin
                        const indentAmount = 40;
                        
                        // Create a range for the current line
                        const lineRange = editor.dom.createRng();
                        
                        if (lineStart) {
                            lineRange.setStartAfter(lineStart);
                        } else {
                            lineRange.setStart(parentElement, 0);
                        }
                        
                        if (lineEnd) {
                            lineRange.setEndBefore(lineEnd);
                        } else {
                            lineRange.setEnd(parentElement, parentElement.childNodes.length);
                        }
                        
                        // Get the line content
                        const lineContent = lineRange.cloneContents();
                        
                        // Check if line is already wrapped in a span with margin
                        let existingSpan = null;
                        if (lineContent.firstChild && lineContent.firstChild.nodeType === 1 && 
                            lineContent.firstChild.tagName === 'SPAN' && 
                            lineContent.childNodes.length === 1) {
                            existingSpan = lineContent.firstChild;
                        }
                        
                        let currentMargin = 0;
                        if (existingSpan) {
                            const marginLeft = existingSpan.style.marginLeft || '0px';
                            currentMargin = parseInt(marginLeft) || 0;
                        }
                        
                        // Calculate new margin
                        let newMargin;
                        if (e.shiftKey) {
                            newMargin = Math.max(0, currentMargin - indentAmount);
                        } else {
                            newMargin = currentMargin + indentAmount;
                        }
                        
                        // Delete the current line content
                        lineRange.deleteContents();
                        
                        // Create new span with margin
                        if (newMargin > 0) {
                            const span = editor.dom.create('span', {
                                style: 'margin-left: ' + newMargin + 'px; display: inline-block;'
                            });
                            
                            // Add the content to the span
                            if (existingSpan) {
                                while (existingSpan.firstChild) {
                                    span.appendChild(existingSpan.firstChild);
                                }
                            } else {
                                while (lineContent.firstChild) {
                                    span.appendChild(lineContent.firstChild);
                                }
                            }
                            
                            lineRange.insertNode(span);
                            
                            // Restore cursor position
                            const newRange = editor.dom.createRng();
                            newRange.setStartAfter(span);
                            newRange.collapse(true);
                            selection.setRng(newRange);
                        } else {
                            // No indent, just insert content without span
                            if (existingSpan) {
                                while (existingSpan.firstChild) {
                                    lineRange.insertNode(existingSpan.firstChild);
                                }
                            } else {
                                while (lineContent.firstChild) {
                                    lineRange.insertNode(lineContent.firstChild);
                                }
                            }
                        }
                    }
                }
            });

            // Format Painter Plugin
            let copiedFormat = null;
            let isPainterActive = false;

            // Add format painter button
            editor.ui.registry.addToggleButton('formatpainter', {
                icon: 'copy',
                tooltip: 'Format Painter (Copy: Click once, Paste: Click on text)',
                onAction: function () {
                    if (!isPainterActive) {
                        // Copy format from current selection
                        const node = editor.selection.getNode();
                        if (node) {
                            copiedFormat = {
                                fontFamily: editor.dom.getStyle(node, 'font-family'),
                                fontSize: editor.dom.getStyle(node, 'font-size'),
                                fontWeight: editor.dom.getStyle(node, 'font-weight'),
                                fontStyle: editor.dom.getStyle(node, 'font-style'),
                                textDecoration: editor.dom.getStyle(node, 'text-decoration'),
                                color: editor.dom.getStyle(node, 'color'),
                                backgroundColor: editor.dom.getStyle(node, 'background-color'),
                                textAlign: editor.dom.getStyle(node, 'text-align'),
                                lineHeight: editor.dom.getStyle(node, 'line-height'),
                                marginLeft: editor.dom.getStyle(node, 'margin-left'),
                                marginRight: editor.dom.getStyle(node, 'margin-right'),
                                paddingLeft: editor.dom.getStyle(node, 'padding-left'),
                                paddingRight: editor.dom.getStyle(node, 'padding-right'),
                                textIndent: editor.dom.getStyle(node, 'text-indent')
                            };
                            isPainterActive = true;
                            editor.notificationManager.open({
                                text: 'Format copied! Click on text to apply the format.',
                                type: 'info',
                                timeout: 3000
                            });
                        }
                    } else {
                        // Cancel format painter
                        isPainterActive = false;
                        copiedFormat = null;
                        editor.notificationManager.open({
                            text: 'Format painter cancelled.',
                            type: 'info',
                            timeout: 2000
                        });
                    }
                },
                onSetup: function (api) {
                    const updateState = function () {
                        api.setActive(isPainterActive);
                    };
                    editor.on('NodeChange', updateState);
                    return function () {
                        editor.off('NodeChange', updateState);
                    };
                }
            });

            // Apply format on click when painter is active
            editor.on('click', function (e) {
                if (isPainterActive && copiedFormat) {
                    const node = editor.selection.getNode();
                    if (node) {
                        // Apply the copied format
                        if (copiedFormat.fontFamily) editor.dom.setStyle(node, 'font-family', copiedFormat.fontFamily);
                        if (copiedFormat.fontSize) editor.dom.setStyle(node, 'font-size', copiedFormat.fontSize);
                        if (copiedFormat.fontWeight) editor.dom.setStyle(node, 'font-weight', copiedFormat.fontWeight);
                        if (copiedFormat.fontStyle) editor.dom.setStyle(node, 'font-style', copiedFormat.fontStyle);
                        if (copiedFormat.textDecoration) editor.dom.setStyle(node, 'text-decoration', copiedFormat.textDecoration);
                        if (copiedFormat.color) editor.dom.setStyle(node, 'color', copiedFormat.color);
                        if (copiedFormat.backgroundColor) editor.dom.setStyle(node, 'background-color', copiedFormat.backgroundColor);
                        if (copiedFormat.textAlign) editor.dom.setStyle(node, 'text-align', copiedFormat.textAlign);
                        if (copiedFormat.lineHeight) editor.dom.setStyle(node, 'line-height', copiedFormat.lineHeight);
                        if (copiedFormat.marginLeft) editor.dom.setStyle(node, 'margin-left', copiedFormat.marginLeft);
                        if (copiedFormat.marginRight) editor.dom.setStyle(node, 'margin-right', copiedFormat.marginRight);
                        if (copiedFormat.paddingLeft) editor.dom.setStyle(node, 'padding-left', copiedFormat.paddingLeft);
                        if (copiedFormat.paddingRight) editor.dom.setStyle(node, 'padding-right', copiedFormat.paddingRight);
                        if (copiedFormat.textIndent) editor.dom.setStyle(node, 'text-indent', copiedFormat.textIndent);

                        editor.notificationManager.open({
                            text: 'Format applied!',
                            type: 'success',
                            timeout: 2000
                        });

                        // Deactivate painter after applying
                        isPainterActive = false;
                        copiedFormat = null;
                    }
                }
            });

            editor.on('init', function () {
                // Call custom init callback if provided
                if (options.onInit) {
                    options.onInit(editor);
                }
            });

            editor.on('change keyup', function () {
                // Call custom change callback if provided
                if (options.onChange) {
                    options.onChange(editor);
                }
            });
        }
    };

    // Merge custom options with defaults
    const config = { ...defaultConfig, ...options };
    
    // Remove custom callbacks from config before passing to tinymce
    delete config.onInit;
    delete config.onChange;

    return tinymce.init(config);
}
