<?php


class Tree_PreOrderVisitor implements Tree_Visitor
{
    public function visit(Tree_NodeInterface $node)
    {
        $nodes = [
            $node,
        ];

        foreach ($node->getChildren() as $child) {
            $nodes = array_merge(
                $nodes,
                $child->accept($this)
            );
        }

        return $nodes;
    }
}
