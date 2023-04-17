<?php

namespace App\Service;

use App\Repository\ResumeRepository;
use App\Repository\UserRepository;

class RandomProfilesService
{
    public function __construct(
        private ResumeRepository $resumeRepository,
        private UserRepository $userRepository
    ) {
    }

    public function getRandomUsers($similarProfilesId): array
    {
        $allSimilarProfilesId = [];
        $similarProfiles = [];
        foreach ($similarProfilesId as $similarProfileId) {
            $allSimilarProfilesId[] = $similarProfileId['id'];
        }
        if (count($allSimilarProfilesId) > 3) {
            $randomsId = array_rand($allSimilarProfilesId, 3);
            foreach ($randomsId as $randomId) {
                $similarProfiles[] = $this->resumeRepository->findOneBy(['id' => $allSimilarProfilesId[$randomId]]);
            }
        } else {
            foreach ($allSimilarProfilesId as $similarProfileId) {
                $similarProfiles[] = $this->resumeRepository->findOneBy(['id' => $similarProfileId]);
            }
        }

        return $similarProfiles;
    }

    public function getRandomCompanies($suggestedCompaniesId): array
    {
        $allSuggestedCompaniesId = [];
        $suggestedCompanies = [];
        foreach ($suggestedCompaniesId as $suggestedCompanyId) {
            $allSuggestedCompaniesId[] = $suggestedCompanyId['id'];
        }
        if (count($allSuggestedCompaniesId) > 3) {
            $randomsId = array_rand($allSuggestedCompaniesId, 3);
            foreach ($randomsId as $randomId) {
                $suggestedCompanies[] = $this->userRepository->findOneBy(['id' => $allSuggestedCompaniesId[$randomId]]);
            }
        } else {
            foreach ($allSuggestedCompaniesId as $suggestedCompanyId) {
                $suggestedCompanies[] = $this->userRepository->findOneBy(['id' => $suggestedCompanyId]);
            }
        }

        return $suggestedCompanies;
    }
}
