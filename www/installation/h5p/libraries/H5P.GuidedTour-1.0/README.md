H5P.GuidedTour
==============

Library for creating an interactive guide. May be usable both by H5P editors and libraries. The library uses the Shepherd library to create the guide. See Shepherd in action here:
<github.hubspot.com/shepherd/docs/welcome/>

In addition to the functionality given by Shepherd, H5P.GuidedTour adds the possibility to highlight the active element with your own css rules.

## Usage

Here is an example from the interactive video editor:

```javascript
var tour = new H5P.GuidedTour([
  {
    text: '<p>This guide tours you through the most important features of the Interactive Video editor</p><p>Press EXIT to skip this tour</p><p>Start this guide by pressing the Tour button in the top right corner</p>',
    attachTo: {element: '.field.wizard .h5peditor-label', on: 'bottom'},
    noArrow: true
  },
  {
    title: 'Adding video',
    text: '<p>Start by adding a video file. You can upload a file from your computer or embed a video from YouTube.</p><p>To ensure compatibility across browsers, you can upload multiple file formats of the same video, such as mp4 as webm</p>',
    attachTo: {element: '.field.video .file', on: 'left'},
    highlightElement: true
  },
  {
    title: 'Adding interactions',
    text: '<p>Once you have added a video, you can start adding interactions</p><p>Press the <em>Add interactions</em> tab to get started</p>',
    attachTo: {element: '.h5peditor-tab-assets', on: 'bottom'},
    highlightElement: true
  }
], {
  highlight: {
    background: '#000',
    color: '#fff'
  }  
});
tour.start();
```
The constructor takes two parameters, where the first one is an array of steps, and the second is some general options. Each step can take the same parameters as Sheperd's addStep, with some modifications:
- step.buttons are set by H5P.GuidedTour (will be overwritten if set from outside)
  - The first step will get a exit and next button
  - The last step will get a back and finish button
  - The steps in between will a get back and next button
- step.highlightElement is only relevant for H5P.GuidedTour. If this value is true, it will highlight the element the step is attached to according to options.highlight (second parameter)
- step.noArrow is only relevant for H5P.GuidedTour. If this value is true, the arrow pointing to the element will not be visible.

H5P.GuidedTour.start() starts the guided tour. It takes one optional boolean parameter (force). If this is true, the guide will be shown even if it has been shown before.

## H5P library dependencies

- [Shepherd](https://github.com/h5p/shepherd)
- [Tether](https://github.com/h5p/tether)

## License

(The MIT License)

Copyright (c) 2015 Joubel AS

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
