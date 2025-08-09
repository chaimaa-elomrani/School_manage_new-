<?php

namespace App\Services;

use App\Interfaces\IObserver;
use App\Models\Note;
use App\Models\Student;

class NotificationService
{
    public function update(string $event, array $data): void
    {
        switch ($event) {
            case 'note_added':
                /** @var Note $note */
                $note = $data['note'];
                /** @var Student $student */
                $student = $data['student'];
                $this->sendNoteNotification($student, $note, "nouvelle note");
                break;
            case 'note_updated':
                /** @var Note $note */
                $note = $data['note'];
                /** @var Student $student */
                $student = $data['student'];
                $this->sendNoteNotification($student, $note, "mise à jour de note");
                break;
            // Add other events like 'bulletin_generated' if needed
            default:
                // Log unknown event
                error_log("Unknown event received by NotificationService: " . $event);
                break;
        }
    }

    private function sendNoteNotification(Student $student, Note $note, string $type): void
    {
        $message = "Cher(e) {$student->getFirstName()} {$student->getLastName()},\n\n";
        $message .= "Vous avez une {$type} de {$note->getValue()} pour l'évaluation ID {$note->getEvaluationId()}.\n\n";
        $message .= "Cordialement,\nVotre établissement.";

        // In a real application, you would send an email or push notification here.
        // For this example, we'll just log it.
        error_log("Notification envoyée à {$student->getEmail()} (ID: {$student->getId()}):\n{$message}\n---");
        echo "Notification sent to {$student->getFirstName()} for note {$note->getValue()}.\n";
    }
}
