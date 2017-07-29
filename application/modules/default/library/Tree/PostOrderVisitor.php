<?php


class PostOrderVisitor implements Tree_Visitor
{
    public function visit(Tree_NodeInterface $node)
    {
        $nodes = [];

        foreach ($node->getChildren() as $child) {
            $nodes = array_merge(
                $nodes,
                $child->accept($this)
            );
        }

        $nodes[] = $node;

        return $nodes;
    }
}
