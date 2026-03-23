<?php

namespace ZzbSdk\Model;

/**
 * 影片分页
 */
class ZzbFilmPage
{
    public ?ZzbPageable $pageable;
    /**
     * @var ZzbFilm[]
     */
    public array $filmList = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['pageable'])) {
            $instance->pageable = ZzbPageable::fromArray($data['pageable']);
        }
        if (isset($data['filmList']) && is_array($data['filmList'])) {
            foreach ($data['filmList'] as $filmData) {
                $instance->filmList[] = ZzbFilm::fromArray($filmData);
            }
        }
        return $instance;
    }
}
