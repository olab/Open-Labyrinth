H5P Editor Radio Selector
==========

Create a radio selector with content connected to each radio button.
Only the selected option's content will be shown.

## Usage

The Radio Selector is a group type with fields, where each field is the content
that will be connected to a radio button. Here is an example :

```json
{
  "name": "globalBackgroundSelector",
  "type": "group",
  "widget": "radioSelector",
  "fields": [
    {
      "name": "imageGlobalBackground",
      "type": "image",
      "label": "Image background",
      "optional": true
    },
    {
      "name": "fillGlobalBackground",
      "type": "text",
      "widget": "colorSelector",
      "label": "Color fill background",
      "spectrum": {
        "flat": true,
        "showInput": true,
        "allowEmpty": true,
        "showButtons": false
      },
      "default": null,
      "optional": true
    }
  ]
}
```

An example of usage is found in the Course Presentation [semantics](https://github.com/h5p/h5p-course-presentation/commit/0cd85d109a2d1d6a1bdb2f0026f3ddffaf17ba53#diff-a8fac9ad0e63f198980962cc2aad2083R261)

## License

(The MIT License)

Copyright (c) 2015 Joubel AS

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
