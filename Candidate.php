<?php
class Candidate
{
    public $voteCount;
    public $candidate_post;
    public $candidate_name;

    public function __construct($candidate_post, $candidate_name, $voteCount)
    {
        $this->candidate_post = $candidate_post;
        $this->candidate_name = $candidate_name;
        $this->voteCount = $voteCount;
    }

    public static function fetchVoterEmails($db)
    {
        $result = $db->query("
        SELECT DISTINCT users.email 
        FROM users 
        JOIN votes ON users.id = votes.user_id
    ");
        $emails = [];
        while ($row = $result->fetch_assoc()) {
            $emails[] = $row['email'];
        }
        return $emails;
    }

    public static function fetchWinners($db)
    {
        $result = $db->query("
            SELECT candidate_post, candidate_name, MAX(vote_count) AS max_votes
    FROM (
        SELECT candidate_post, candidate_name, COUNT(*) AS vote_count
        FROM votes
        GROUP BY candidate_id, candidate_post
    ) AS vote_counts
    GROUP BY candidate_post
        ");

        $winners = [];
        while ($row = $result->fetch_assoc()) {
            $winners[] = new self($row['candidate_post'], $row['candidate_name'], $row['max_votes']);
        }

        return $winners;
    }
}
