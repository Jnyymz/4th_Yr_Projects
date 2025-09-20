<?php
require_once "Database.php";

class Proposal {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllProposals() {
        $sql = "SELECT * FROM proposals ORDER BY date_added DESC";
        return $this->db->executeQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProposalsByCategory($categoryId = 0){
        $sql = "SELECT p.proposal_id,
                       p.user_id,
                       p.image AS proposal_image,
                       p.date_added,
                       p.description,
                       p.min_price,
                       p.max_price,
                       u.username,
                       c.category_name
                FROM proposals p
                JOIN fiverr_clone_users u ON p.user_id = u.user_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.category_id = ?
                ORDER BY p.date_added DESC";
        return $this->db->executeQuery($sql, [$categoryId])->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getProposalsWithCategory() {
        $sql = "SELECT p.proposal_id,
                    p.user_id,
                    p.image AS proposal_image,
                    p.date_added,
                    p.description,
                    p.min_price,
                    p.max_price,
                    u.username,
                    c.category_name
                FROM proposals p
                JOIN fiverr_clone_users u ON p.user_id = u.user_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                ORDER BY p.date_added DESC";
        return $this->db->executeQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

}
