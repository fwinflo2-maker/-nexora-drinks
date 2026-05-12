<?php

namespace App\Enums;

enum AutomationAction: string
{
    case BlockOrder = 'block_order';
    case CreatePurchaseSuggestion = 'create_purchase_suggestion';
    case SendDebtReminder = 'send_debt_reminder';
    case AlertManager = 'alert_manager';
    case SendNotification = 'send_notification';

    public function label(): string
    {
        return match ($this) {
            self::BlockOrder => 'Bloquer la commande',
            self::CreatePurchaseSuggestion => 'Créer une suggestion d\'achat',
            self::SendDebtReminder => 'Envoyer un rappel de dette',
            self::AlertManager => 'Alerter le manager',
            self::SendNotification => 'Envoyer une notification',
        };
    }
}
