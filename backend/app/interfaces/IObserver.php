<?php

namespace App\Interfaces;

interface IObserver
{
    /**
     * Receive update from subject.
     * @param string $event The event that occurred (e.g., 'note_added', 'note_updated').
     * @param array $data Associated data with the event (e.g., ['note' => Note, 'student' => Student]).
     */
    public function update(string $event, array $data): void;
}
