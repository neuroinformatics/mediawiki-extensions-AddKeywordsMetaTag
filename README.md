# MediaWiki Extension AddKeywordsMetaTag

This extension generates `keywords` meta tag header from MediaWiki:Keywords page and `<keywords content=".."/>` tags.

## Install

To install this extension, add the following to LocalSettings.php.

```PHP
wfLoadExtension("AddKeywordsMetaTag");
```

## Usage

#### global keywords

Edit `MediaWiki:Keywords` page.

#### page specific keywords

```MediaWiki
<keywords content="foo, bar" />
```

## License

This software is licensed under the [MIT](COPYING).

## Authors

- [Yoshihiro Okumura](https://github.com/orrisroot)

## Usage examples

- https://bsd.neuroinf.jp/ : Brain Science Dictionary project in Japanese.
