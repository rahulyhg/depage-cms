<?php

namespace Depage\Cms\XmlDocTypes;

class Page extends Base
{
    use Traits\MultipleLanguages;

    private $table_nodetypes;
    private $pathXMLtemplate = "";

    // {{{ constructor
    public function __construct($xmlDb, $document) {
        parent::__construct($xmlDb, $document);

        $this->pathXMLtemplate = $this->xmlDb->options['pathXMLtemplate'];

        $this->table_nodetypes = $xmlDb->table_nodetypes;
    }
    // }}}

    // {{{ onAddNode
    /**
     * On Add Node
     *
     * @param \DomNode $node
     * @param $target_id
     * @param $target_pos
     * @param $extras
     * @return null
     */
    public function onAddNode(\DomNode $node, $target_id, $target_pos, $extras) {
        $this->testNodeLanguages($node);

        list($doc, $node) = \Depage\Xml\Document::getDocAndNode($node);

        $xpath = new \DOMXPath($doc);

        $nodelist = $xpath->query("./edit:date[@value = '@now']", $node);
        if ($nodelist->length > 0) {
            // search for languages used in document
            for ($i = 0; $i < $nodelist->length; $i++) {
                $nodelist->item($i)->setAttribute('value', date('Y/m/d'));
            }
        }

        $nodelist = $xpath->query("./edit:text_singleline[@value = '@author']", $node);
        if ($nodelist->length > 0) {
            // search for languages used in document
            for ($i = 0; $i < $nodelist->length; $i++) {
                if (!empty($this->xmlDb->options['userId'])) {
                    $user = \Depage\Auth\User::loadById($this->xmlDb->pdo, $this->xmlDb->options['userId']);
                    $nodelist->item($i)->setAttribute('value', $user->fullname);
                } else {
                    $nodelist->item($i)->setAttribute('value', "");
                }
            }
        }
    }
    // }}}
    // {{{ onDocumentChange()
    /**
     * @brief onDocumentChange
     *
     * @param mixed
     * @return void
     **/
    public function onDocumentChange()
    {
        $this->xmlDb->getDoc("pages")->clearCache();

        parent::onDocumentChange();

        return true;

    }
    // }}}

    // {{{ addNodeType
    public function addNodeType($nodeName, $options) {
        $name = $options['name'];
        $data = [
            'pos' => 0,
            'name' => $name,
            'newName' => $name,
            'icon' => '',
            'xmlTemplate' => '',
        ];
        foreach ($data as $key => $value) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            }
        }
        $data['nodeName'] = $nodeName;
        if (isset($options['validParents'])) {
            $data['validParents'] = implode(",", $options['validParents']);
        } else {
            $data['validParents'] = "*";
        }

        $query = $this->xmlDb->pdo->prepare(
            "INSERT {$this->table_nodetypes} SET
                pos = :pos,
                nodename = :nodeName,
                name = :name,
                newname = :newName,
                validparents = :validParents,
                icon = :icon,
                xmltemplate = :xmlTemplate;"
        );
        $query->execute($data);

        if (!empty($options['xmlTemplateData']) && (!empty($options['xmlTemplate']))) {
            file_put_contents($this->pathXMLtemplate . $options['xmlTemplate'], $options['xmlTemplateData']);
        }
    }
    // }}}
    // {{{ getNodeTypes
    public function getNodeTypes() {
        // @todo get these from xml files instead of from database
        $nodetypes = [];
        $query = $this->xmlDb->pdo->prepare(
            "SELECT
                id,
                nodename as nodeName,
                name as name,
                newname as newName,
                validparents as validParents,
                icon as icon,
                xmltemplate as xmlTemplate
            FROM {$this->table_nodetypes} ORDER BY pos;"
        );
        $query->execute();

        do {
            $result = $query->fetchObject();

            if ($result) {
                $nodetypes[$result->id] = $result;
                $templatePath = $this->pathXMLtemplate . $result->xmlTemplate;

                // load template data
                $xml = new \Depage\Xml\Document();
                $xml->load($templatePath);

                $data = "";
                foreach ($xml->documentElement->childNodes as $node) {
                    if ($node->nodeType != \XML_COMMENT_NODE) {
                        $data .= $xml->saveXML($node);
                    }
                }
                $nodetypes[$result->id]->xmlTemplateData = $data;

                // get date of last change
                $nodetypes[$result->id]->lastchange = filemtime($templatePath);
            }
        } while ($result);

        return $nodetypes;
    }
    // }}}

    // {{{ testDocument
    public function testDocument($node) {
        $changed = $this->testNodeLanguages($node);

        $this->addReleaseStatusAttributes($node->firstChild);

        return $changed;
    }
    // }}}
    // {{{ testDocumentForHistory
    public function testDocumentForHistory($xml) {
        parent::testDocumentForHistory($xml);

        $xml->firstChild->setAttributeNS("http://cms.depagecms.net/ns/database", "db:released", "true");
    }
    // }}}
    // {{{ addReleaseStatusAttributes()
    /**
     * @brief addReleaseStatusAttributes
     *
     * @param mixed $
     * @return void
     **/
    public function addReleaseStatusAttributes($node)
    {
        $info = $this->document->getDocInfo();
        $versions = array_values($this->document->getHistory()->getVersions(true, 1));

        // @todo fix not to get from timestamp when sha1 did no change
        if (count($versions) > 0 && $info->lastchange->getTimestamp() < $versions[0]->lastsaved->getTimestamp()) {
            $node->setAttributeNS("http://cms.depagecms.net/ns/database", "db:released", "true");
        } else {
            $node->setAttributeNS("http://cms.depagecms.net/ns/database", "db:released", "false");
        }
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
