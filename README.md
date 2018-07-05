## gitignore
- offline-page.html

## Fitur
- Offline Mode
- LazyLoad
- Set App Name
- Set Background Color
- Set Theme Color

## Avalable Shortcode
- [pwa-optimizer-lazyload]

### Shortcode Attribute
- **type** (optional), *image* or *iframe*. (Default: image)
- **src** (required), source
- **alt** (optional), alt image attribute if type is image
- **id** (optional), attribute id
- **class** (optional), attribute class
- **style** (optional), attribute style
- **width** (optional), attribute width
- **height** (optional), attribute height

## Development & Testing Notes

To make sure expected changes on sw.js applied on your Chrome browser, please do one of two methods below after the changes:

Method 1:

1. Open Inspect Element
2. Select Aplication tab (inside >> button)
3. Select "Service Workers" menu
4. Check 'Update on reload`
5. Refresh your page using Ctrl+F5

Method 2:

1. Open Inspect Element
2. Select Aplication tab (inside >> button)
3. Select "Service Workers" menu
4. Click `update` on desired service workers installed on your Chrome