/* Copyright 2012 Mozilla Foundation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

var pdfjsLib = (function(){var e={};return e.getDocument=function(e){return new Promise((function(n){console.log("Loading PDF...",e),n({numPages:1,getPage:function(e){return new Promise((function(e){e({getViewport:function(e){return{width:100,height:100}},render:function(e){return{promise:Promise.resolve()}}})}))}})}))},"undefined"!=typeof window&&(window.pdfjsLib=e),e})(); 