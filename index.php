<?php

// Only valid if PHP7 or greater
//declare(strict_types=1);

/**
 * AUTHOR : AVONTURE Christophe.
 *
 * Written date : 6 october 2018
 *
 * JSON lint
 * A very simple JSON lint interface, no server-side processing, everything
 * done by Javascript
 *
 * @see https://stackoverflow.com/a/7220510/1065340 for the color output
 *
 * jQuery plugin for showing JSON with expand / collapse feature
 * @see https://github.com/bazh/jquery.json-view
 *
 * Get the code on GitHub: https://github.com/cavo789/jsonlint
 *
 * Last mod:
 * 2019-01-01 - Abandonment of jQuery and migration to vue.js
 *      (except for jquery.json-view which require jQuery)
 */

define('REPO', 'https://github.com/cavo789/jsonlint');

 // Sample content
$json = '{"name":"John","age":30,"cars":["Ford","BMW","Fiat"],' .
    '"places":["Africa","America","Asia","Australia"]}';

// Get the GitHub corner
$github = '';
if (is_file($cat = __DIR__ . DIRECTORY_SEPARATOR . 'octocat.tmpl')) {
    $github = str_replace('%REPO%', REPO, file_get_contents($cat));
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="author" content="Christophe Avonture" />
        <meta name="robots" content="noindex, nofollow" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8;" />
        <title>JSONLint & Pretty print</title>
        <style>
            .string  { color: green; }
            .number  { color: darkorange; }
            .boolean { color: blue; }
            .null    { color: magenta; }
            .key     { color: red; }
            .info    { display: block; font-size: 0.6em }
        </style>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="libs/jquery.json-view.css">
    </head>
    <body>
        <?php echo $github; ?>
        <div class="container">
            <div class="page-header"><h1>JSONLint & Pretty print</h1></div>
            <div class="container" id="app">
                <div class="form-group">
                    <how-to-use demo="https://raw.githubusercontent.com/cavo789/jsonlint/master/images/demo.gif"></how-to-use>
                    <label for="JSON">Copy/Paste your JSON content in the textbox below, linting will be made automatically:</label>
                    <textarea class="form-control" rows="5" name="JSON" v-model="JSON"></textarea>
                </div>
                <div class="row">
                    <div class="form-group form-inline">
                        <div class="radio mr-sm-3">
                            <label>
                                <input name="optFeature" type="radio" @click="doHTML" checked="checked">&nbsp;HTML with colors
                            </label>
                        </div>
                        <div class="radio mr-sm-3">
                            <label>
                                <input name="optFeature" type="radio" @click="doCollapse">&nbsp;With expand/collapse feature
                            </label>
                        </div>
                    </div>
                </div>
                <hr/>
                <div v-if="errors.length">
                    <div id="ErrorMessage">
                        <b>Your JSON isn't valid (perhaps a double-quote is missing). Please correct the following error(s):</b>
                        <ul>
                            <li v-for="error in errors">{{ error }}</li>
                        </ul>
                    </div>
                </div>
                <pre v-html="HTML" class="language-json"></pre>

                <!-- Needed for jquery.json-view -->
                <div id="Result" style="display:none;"></div>

                <i class="info">
                    <a href="https://github.com/bazh/jquery.json-view">
                        JSON Collapse jQuery plugin of Anton Bazhenov
                    </a>
                </i>
            </div>
        </div>
        <script src="https://unpkg.com/vue"></script>
        
        <!-- jQuery is required by jquery.json-view.js -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="libs/jquery.json-view.js"></script>

        <script type="text/javascript">
            //https://stackoverflow.com/a/7220510/1065340
            function syntaxHighlight(json) {
                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    var cls = 'number';
                    if (/^"/.test(match)) {
                            if (/:$/.test(match)) {
                                cls = 'key';
                            } else {
                                cls = 'string';
                            }
                    } else if (/true|false/.test(match)) {
                            cls = 'boolean';
                    } else if (/null/.test(match)) {
                            cls = 'null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                });
            }

            Vue.component('how-to-use', {
                props: {
                    demo: {
                        type: String,
                        required: true
                    }
                },
                template:
                    `<details>
                        <summary>How to use?</summary>
                        <div class="row">
                            <div class="col-sm">
                                <ul>
                                    <li>Copy/Paste your JSON content in the textbox below.</li>
                                    <li>Make a choice between "HTML with colors" or "With expand/collapse feature"</li>                                        
                                </ul>
                            </div>
                            <div class="col-sm"><img v-bind:src="demo" alt="Demo"></div>
                        </div>
                    </details>`
            });

            var app = new Vue({
                el: '#app',
                data: {
                    JSON: '{"name":"John","age":30,"cars":["Ford","BMW","Fiat"],"places":["Africa","America","Asia","Australia"]}',
                    lintHTML: true,
                    lintCollapse: false,
                    allow: true,
                    errors: []
                },
                methods: {
                    doHTML() {
                        // Linting will show an HTML rendering with colors
                        this.lintHTML = true;
                        this.lintCollapse = false;
                    },
                    doCollapse() {
                        // Linting will show a expand/collapse JSON treeview
                        this.lintCollapse = true;
                        this.lintHTML = false;
                    }
                },
                computed: {
                    HTML() {
                        // Initialization
                        this.allow = true;
                        this.errors = [];

                        try {
                            // Convert as an array
                            var $JSON = JSON.parse(this.JSON);
                        } catch (error) {
                            // Ouch, an error has been encountered.
                            // The JSON wasn't valid (f.i. {name:"John"} instead of {"name":"John"}))
                            // Try to solve thanks the eval() statement
                            try {
                                this.JSON = JSON.stringify(eval('('+this.JSON+')'));

                                // Not in the catch statement? Ok, we've a valid JSON now
                                $JSON = JSON.parse(this.JSON);
                            } catch (error) {
                                // Still with errors (f.i. {name:John} )
                                // Capture the error and don't allow the linting
                                this.errors.push(error.message);
                                this.allow = false;
                            }
                        }

                        if(this.allow) {
                            this.errors = [];

                            if(this.lintHTML) {
                                // HTML rendering : use standard javascript (thus Vue); no jQuery
                                $('#Result').html('').hide();
                                return syntaxHighlight(JSON.stringify($JSON, undefined, 2));
                            }
    
                            // Collapse/expand rendering : use jQuery, not Vue
                            $('#Result')
                                .html('')
                                .jsonView(JSON.stringify($JSON, undefined, 2))
                                .show();
                        }
                        return '';
                    }
                }
            });
        </script>
    </body>
</html>
