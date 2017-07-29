<?php
/*
 * This file is part of Tree library.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Visitor interface for Nodes
 *
 * @package    Tree
 * @author     Nicolò Martini <nicmartnic@gmail.com>
 */
interface Tree_Visitor
{
    /**
     * @param Tree_NodeInterface $node
     * @return mixed
     */
    public function visit(Tree_NodeInterface $node);
}