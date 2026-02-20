<?php

namespace App\Scrapers\Contracts;

interface MotoScraperInterface
{
    /** Code unique du site : dafy, speedway, etc */
    public function getSiteCode(): string;

    /** Liste des catégories [code => url] */
    public function getCategories(): array;

    /** Récupère des URLs produits pour une catégorie */
    public function getProductUrls(string $categoryUrl, int $limit = 30): array;

    /** Parse un produit */
    public function parseProduct(string $url): array;
}


