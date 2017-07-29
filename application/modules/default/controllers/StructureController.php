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

        $html .= '<li class="' . $data['class'] . '"><i></i><p>' . $name . '</p>';

        if ($node->isLeaf()) {
            $html .= '</li>';

            return $html;
        }

        $html .= '<ul>';
        $children = $node->getChildren();
        foreach ($children as $child) {
            $html .= $this->renderTree($child);
        }
        $html .= '</ul>';
        $html .= '</li>';

        return $html;
    }
	
	public function indexAction()
	{
		$structureModel = new Default_Model_Structure();
		$orgData = $structureModel->getOrgData();
		$unitData = $structureModel->getUnitData();
		$deptData = $structureModel->getDeptData();
		$nobu = 'no';
		foreach($deptData as $rec)
		{			
			if($rec['unitid'] == '0')
			$nobu = 'exists';
			
		}

		$orgData['class'] = 'orgunit';
		$orgTree = new Tree_Node($orgData);

		foreach ($unitData as $unit) {
		    $unit['class'] = 'bunitclass';

		    $child = new Tree_Node($unit);
		    $orgTree->addChild($child);
        }

        $departmentData = $deptData;
        $dept = current($departmentData);
        while (!empty($departmentData)) {

            $visitor = new Tree_PreOrderVisitor;

            $children = array_filter($orgTree->accept($visitor), function ($k) {
                $data = $k->getValue();
                return $data['class'] != 'orgunit';
            });
            foreach ($children as $child) {
                $unitdata = $child->getValue();

                if (($dept['deptid'] == null && $unitdata['id'] == $dept['unitid']) ||
                    isset($unitdata['unitid']) &&
                    $dept['deptid'] == $unitdata['id']  && $dept['unitid'] == $unitdata['unitid']) {

                    $dept['class'] = 'deptclass';
                    $grandchild = new Tree_Node($dept);
                    $child->addChild($grandchild);

                    $key = key($departmentData);
                    unset($departmentData[$key]);
                    $dept = current($departmentData);
                }
            }
        }

        $this->view->controller = $this;
        $this->view->orgTree = $orgTree;
		$this->view->orgData = $orgData;
		$this->view->msg = 'This is organization structure';
	}
}
?>