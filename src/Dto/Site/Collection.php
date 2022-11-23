<?php

declare(strict_types=1);

namespace JTG\Mark\Dto\Site;

use JTG\Mark\Dto\Context\Config\Collection as CollectionConfig;

class Collection
{
    private ?string $name = null;
    private ?string $template = null;
    private ?bool $output = false;

    private array $items = [];

    public static function fromCollectionConfig(CollectionConfig $collectionConfig): Collection
    {
        return (new self())
            ->setName($collectionConfig->name)
            ->setTemplate($collectionConfig->template)
            ->setOutput($collectionConfig->output);
    }

    # region getter / setter

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getOriginalName(): ?string
    {
        return $this->name === 'root' ? '' : $this->name;
    }

    public function setName(?string $name): Collection
    {
        $this->name = $name;
        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): Collection
    {
        $this->template = $template;
        return $this;
    }

    public function getOutput(): ?bool
    {
        return $this->output;
    }

    public function setOutput(?bool $output): Collection
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return array<File>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getItem(string $relativePathname): ?File
    {
        return $this->items[$relativePathname] ?? null;
    }

    /**
     * @param array<File> $items
     */
    public function setItems(array $items): Collection
    {
        $this->items = [];

        foreach ($items as $item) {
            $this->addItem(item: $item);
        }

        return $this;
    }

    public function addItem(File $item): Collection
    {
        $this->items[$item->getId()] = $item;
        $item->setCollection($this);

        return $this;
    }

    public function getItemsCount(): int
    {
        return count($this->items);
    }

    # endregion getter / setter
}