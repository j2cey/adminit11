<?php

namespace App\Enums\Settings\Branches;

use App\Enums\Settings\SettingNode;

/**
 * Class Treatment. Raw Treatment settings
 * @package App\Enums\Settings\Branches
 *
 * @method activate()
 * @method max_retries()
 * @method max_running()
 * @method formatcolumns()
 */
class Treatment extends SettingNode
{
    public function __construct()
    {
        parent::__construct("treatment",null,null,null,null,"settings ReportTreatments.");

        $this->addChild("activate", "1", "bool", "active ou desactive les traitements de Rapports.");
        $this->addChild("max_retries", "10", "integer", "nombre max de tentatives de retraitement.");
        $this->addChild("formatcolumns", null, null, "Formattage des colonnes.")
            ->addChild("append_batch_max", "10", "integer", "nombre max de colonnes a ajouter au batch de traitement de la ligne.");

        $this->addChild("merge_file", null, null, "Merge Files.")
            ->addChild("max_retries", "10", "integer", "nombre max de tentatives de retraitement de Merge de fichier.");

        $max_running = $this->addChild("max_running", null, null, "Nombre Max d execution autorise par code de traitement.");
        $max_running->addChild("downloadfile", "10", "integer", "nombre max d executions pour downloadfile.");
        $max_running->addChild("importfile", "1", "integer", "nombre max d executions pour importfile.");
        $max_running->addChild("formatfile", "1", "integer", "nombre max d executions pour formatfile.");
        $max_running->addChild("mergefile", "10", "integer", "nombre max d executions pour mergefile.");
        $max_running->addChild("notifyfile", "10", "integer", "nombre max d executions pour notifyfile.");
    }
}
