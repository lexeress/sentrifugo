<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
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

class Default_Model_Structure extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_businessunits';
    protected $_primary = 'id';
	
	public function getOrgData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$orgData = $db->query("select id,organisationname from main_organisationinfo where isactive = 1;");
		$result= $orgData->fetch();
		return $result;
	}
	
	public function getUnitData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$unitData = $db->query("select id,unitname from main_businessunits where isactive = 1 order by unitname asc;");
		$result= $unitData->fetchAll();
		return $result;
	}
	
	public function getDeptData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$deptData = $db->query("select id,deptname,unitid,deptid from main_departments where isactive = 1 order by deptname asc;");
		$result= $deptData->fetchAll();
		return $result;
	}

	public function getOrgTree()
    {
        $orgData = $this->getOrgData();
        $unitData = $this->getUnitData();
        $deptData = $this->getDeptData();
        $nobu = 'no';
        foreach($deptData as $rec)
        {
            if($rec['unitid'] == '0')
                $nobu = 'exists';

        }

        if ($nobu == 'no') {
            $unitData = array_filter($unitData, function($k) { return $k['id'] != '0'; });
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

            $dept = next($departmentData);
            if ($dept == false) {
                $dept = reset($departmentData);
            }
        }

        return $orgTree;
    }
	
}