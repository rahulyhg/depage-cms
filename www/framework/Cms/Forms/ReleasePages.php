<?php

namespace Depage\Cms\Forms;

/**
 * brief Project
 * Class Project
 */
class ReleasePages extends \Depage\HtmlForm\HtmlForm
{
    // {{{ __construct()
    /**
     * @brief __construct
     *
     * @param mixed $name, $params
     * @return void
     **/
    public function __construct($name, $params = [])
    {
        $params['cancelUrl'] = DEPAGE_BASE;
        $params['cancelLabel'] = _("Cancel");
        $params['class'] = "lastchanged_pages";

        $this->project = $params['project'];
        $this->users = $params['users'];
        $this->selectedDocId = !empty($params['selectedDocId']) ? $params['selectedDocId'] : "";

        parent::__construct($name, $params);

        $this->label = _("Release Pages Now");
    }
    // }}}
    // {{{ addChildElements()
    /**
     * @brief addChildElements
     *
     * @return void
     **/
    public function addChildElements()
    {
        $formatter = new \Depage\Formatters\DateNatural();

        $pages = $this->project->getUnreleasedPages();
        $previewPath = $this->project->getPreviewPath();

        $fs = $this->addFieldset("recentChanges", [
            'label' => _("Unreleased Pages"),
            'class' => "select-all",
        ]);
        $fs->addHtml("<p>" . _("Please select the pages you want to release:") . "</p>");

        foreach($pages as $page) {
            if (!$page->released) {
                $user = $this->users[$page->lastchangeUid];
                $selected = $page->name == $this->selectedDocId;

                $fs->addHtml("<a href=\"" . $previewPath . $page->url . "\" class=\"button preview\" target=\"previewFrame\">" . _("Preview") . "</a>");
                $fs->addBoolean("page-" . $page->id, array(
                    'label' => $page->url,
                    'defaultValue' => $selected,
                ));
                $fs->addHtml("<p class=\"small\">" . sprintf(_("Changed by %s, %s"), $user->fullname, $formatter->format($page->lastchange, true)) . "</p>");
            }
        }
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
