<div
    id="node_<?php self::t($this->root_id); ?>"
    class="jstree-container"
    data-doc-id="<?php self::t($this->doc_id); ?>"
    data-seq-nr="<?php self::t($this->seq_nr); ?>"
    data-selected-nodes=""
    data-open-nodes=""
    data-theme="framework/cms/css/jstree.css"
    data-tree-url="<?php self::a($this->treeUrl, "auto"); ?>"
    data-delta-updates-websocket-url=""
    data-delta-updates-fallback-poll-url="<?php self::a($this->actionUrl . "fallback/updates/", "auto"); ?>"
    data-delta-updates-post-url="<?php self::a($this->actionUrl, "auto"); ?>"
    data-types-settings-url="<?php self::a($this->actionUrl . "types-settings/", "auto"); ?>"
    data-add-marker-special-children="folder separator"
>
    <!--data-delta-updates-websocket-url="ws://127.0.0.1:8000/jstree/"-->
    <?php self::e($this->nodes); ?>
</div>
<?php // vim:set ft=php sw=4 sts=4 fdm=marker et :
