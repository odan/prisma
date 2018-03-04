<?php

namespace App\Entity;

use PDO;

abstract class Mapper
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
}

class TicketMapper extends Mapper
{
    public function findTickets()
    {
        $sql = "SELECT t.id, t.title, t.description, c.component
            FROM tickets t
            JOIN components c ON (c.id = t.component_id)";

        $stmt = $this->db->query($sql);

        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new TicketEntity($row);
        }

        return $results;
    }
}


class TicketEntity
{
    /** @var int|null */
    protected $id;

    /** @var string|null */
    protected $title;

    /** @var string|null */
    protected $description;

    /** @var string|null */
    protected $component;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array|null $data The data to use to create
     */
    public function __construct(array $data = null)
    {
        // Hydration (manually)
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }
        if (isset($data['title'])) {
            $this->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }
        if (isset($data['component'])) {
            $this->setComponent($data['component']);
        }
    }

    /**
     * Get Id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set Id.
     *
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get Title.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set Title.
     *
     * @param string|null $title
     * @return void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get Description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get short description.
     *
     * @return string|null
     */
    public function getShortDescription()
    {
        return substr($this->description, 0, 20);
    }

    /**
     * Set Description.
     *
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get Component.
     *
     * @return string|null
     */
    public function getComponent(): ?string
    {
        return $this->component;
    }

    /**
     * Set Component.
     *
     * @param string|null $component
     * @return void
     */
    public function setComponent(?string $component): void
    {
        $this->component = $component;
    }

}