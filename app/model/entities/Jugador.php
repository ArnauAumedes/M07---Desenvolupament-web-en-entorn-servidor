<?php

class Jugador {
    private ?int $id;
    private string $nombre_completo;
    private int $equipo_id;
    private float $valor;
    private int $partidos;
    private int $goles;
    private int $asistencias;

    public function __construct(
        ?int $id = null,
        string $nombre_completo = '',
        int $equipo_id = 0,
        float $valor = 0.00,
        int $partidos = 0,
        int $goles = 0,
        int $asistencias = 0
    ) {
        $this->id = $id;
        $this->nombre_completo = $nombre_completo;
        $this->equipo_id = $equipo_id;
        $this->valor = $valor;
        $this->partidos = $partidos;
        $this->goles = $goles;
        $this->asistencias = $asistencias;
    }

    public function getId(): ?int {
        return $this->id;
    }
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getNombreCompleto(): string {
        return $this->nombre_completo;
    }
    public function setNombreCompleto(string $nombre_completo): void {
        $this->nombre_completo = $nombre_completo;
    }

    public function getEquipoId(): int {
        return $this->equipo_id;
    }
    public function setEquipoId(int $equipo_id): void {
        $this->equipo_id = $equipo_id;
    }

    public function getValor(): float {
        return $this->valor;
    }
    public function setValor(float $valor): void {
        $this->valor = $valor;
    }

    public function getPartidos(): int {
        return $this->partidos;
    }
    public function setPartidos(int $partidos): void {
        $this->partidos = $partidos;
    }

    public function getGoles(): int {
        return $this->goles;
    }
    public function setGoles(int $goles): void {
        $this->goles = $goles;
    }

    public function getAsistencias(): int {
        return $this->asistencias;
    }
    public function setAsistencias(int $asistencias): void {
        $this->asistencias = $asistencias;
    }
}
