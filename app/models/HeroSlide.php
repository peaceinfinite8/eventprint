<?php
// app/models/HeroSlide.php

class HeroSlide
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getActiveByPage(string $pageSlug = 'home'): array
    {
        $sql = "SELECT id, page_slug, title, subtitle, badge, cta_text, cta_link, image, position, is_active
                FROM hero_slides
                WHERE page_slug = :page AND is_active = 1
                ORDER BY position ASC, id ASC";
        $st = $this->pdo->prepare($sql);
        $st->execute([':page' => $pageSlug]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
