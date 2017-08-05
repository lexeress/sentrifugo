<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/

class Default_StructureController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function renderTree($node)
    {
        $html = '';

        $data = $node->getValue();
        $name = isset($data['organisationname']) ? $data['organisationname'] :
            (isset($data['unitname']) ? $data['unitname'] : $data['deptname']);

        if ($data['id'] != '0') {
            $html .= '<li class="' . $data['class'] . '"><i></i><p>' . $name . '</p>';
        }

        if ($node->isLeaf()) {
            $html .= '</li>';

            return $html;
        }

        if ($data['id'] != '0') {
            $html .= '<ul>';
        }
        $children = $node->getChildren();
        foreach ($children as $child) {
            $html .= $this->renderTree($child);
        }
        if ($data['id'] != '0') {
            $html .= '</ul>';
        }
        $html .= '</li>';

        return $html;
    }
	
	public function indexAction()
	{
		$structureModel = new Default_Model_Structure();
		$orgTree = $structureModel->getOrgTree();

        $this->view->controller = $this;
        $this->view->orgTree = $orgTree;
		$this->view->msg = 'This is organization structure';
	}
}
?>