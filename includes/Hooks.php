<?php

namespace MediaWiki\Extension\AddKeywordsMetaTag;

use MediaWiki\Revision\SlotRecord;

class Hooks
{
    /**
     * register tag renderer callbacks.
     */
    public static function onParserFirstCallInit(\Parser $parser)
    {
        $parser->setHook('keywords', [self::class, 'renderKeywordsTag']);
    }

    /**
     * render <keywords> tag.
     *
     * @param string $input
     *
     * @return string
     */
    public static function renderKeywordsTag($input, array $params, \Parser $parser, \PPFrame $frame)
    {
        if (!isset($params['content'])) {
            return '<div class="errorbox">Error: &lt;keywords&gt; tag must contain a &quot;content&quot; attribute.</div>';
        }

        return '<!-- META_KEYWORDS '.base64_encode($params['content']).' -->';
    }

    /**
     * on output page before html.
     *
     * @param string &$text
     */
    public static function onOutputPageBeforeHTML(\OutputPage $out, &$text)
    {
        global $action;
        $keywords = [];
        if (in_array($action, ['edit', 'history', 'delete', 'watch'])) {
            return;
        }
        // get keywords from MediaWiki:Keywords page.
        $title = \Title::MakeTitle(NS_MEDIAWIKI, 'Keywords');
        $page = \WikiPage::factory($title);
        $revision = $page->getRevisionRecord();
        $content = $revision->getContent(SlotRecord::MAIN);
        foreach (explode(',', \ContentHandler::getContentText($content)) as $keyword) {
            $keyword = trim($keyword);
            if ('' !== $keyword) {
                $keywords[] = $keyword;
            }
        }
        // get keywords from rendered <keywords> tags.
        if (preg_match_all('/<!-- META_KEYWORDS ([0-9a-zA-Z\\+\\/]+=*) -->/m', $text, $matches)) {
            $data = $matches[1];
            // Merge keyword data into OutputPage as meta tags
            foreach ($data as $item) {
                $content = @base64_decode($item);
                foreach (explode(',', $content) as $keyword) {
                    $keyword = trim($keyword);
                    if ('' !== $keyword) {
                        $keywords[] = $keyword;
                    }
                }
            }
        }
        // add keywords meta tag.
        $keywords = array_unique($keywords);
        if (!empty($keywords)) {
            $out->addMeta('keywords', implode(', ', $keywords));
        }
    }
}
