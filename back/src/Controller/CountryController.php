<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\CountryRepository;
use Soosuuke\IaPlatform\Entity\Country;

class CountryController
{
    private CountryRepository $countryRepository;

    public function __construct()
    {
        $this->countryRepository = new CountryRepository();
    }

    // GET /countries
    public function getAllCountries(): array
    {
        return $this->countryRepository->findAll();
    }

    // GET /countries/{id}
    public function getCountryById(int $id): ?Country
    {
        return $this->countryRepository->findById($id);
    }

    // POST /countries
    public function createCountry(array $data): Country
    {
        $country = new Country($data['name']);
        $this->countryRepository->save($country);
        return $country;
    }

    // PUT /countries/{id}
    public function updateCountry(int $id, array $data): ?Country
    {
        $country = $this->countryRepository->findById($id);
        if (!$country) {
            return null;
        }

        $country = new Country($data['name'] ?? $country->getName());
        $this->countryRepository->update($country);
        return $country;
    }

    // DELETE /countries/{id}
    public function deleteCountry(int $id): bool
    {
        $country = $this->countryRepository->findById($id);
        if (!$country) {
            return false;
        }

        $this->countryRepository->delete($id);
        return true;
    }
}
