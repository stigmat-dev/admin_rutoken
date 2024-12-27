#!/bin/bash

icon_path="image/ico.png"
so_pin="87654321"

format_device() {
    params=$(zenity --forms --title="Форматирование" \
        --window-icon="$icon_path" \
        --add-entry="Новый PIN пользователя" \
        --add-entry="Новый PIN администратора" \
        --add-entry="Метка устройства" \
        --add-entry="Минимальный PIN администратора" \
        --add-entry="Минимальный PIN пользователя" \
        --add-entry="Макс. попыток PIN администратора" \
        --add-entry="Макс. попыток PIN пользователя" \
        )
    

    if [ $? -ne 0 ]; then
        return
    fi

    IFS='|' read -r new_so_pin new_user_pin rutoken_label min_so_pin min_user_pin max_so_pin_retry_count max_user_pin_retry_count <<<"$params"

    if sudo ./kernel/rtadmin format -p "$new_so_pin" --new-user-pin "$new_user_pin" --new-so-pin "$so_pin" -l "$rutoken_label" --min-so-pin "$min_so_pin" --min-user-pin "$min_user_pin" --max-so-pin-retry-count "$max_so_pin_retry_count" --max-user-pin-retry-count "$max_user_pin_retry_count" --pin-change-policy so; then
        zenity --info --text="Форматирование прошло успешно."
    else
        zenity --error --text="Ошибка при форматировании."
    fi
}

set_user_pin() {
    so_pin=$(zenity --password --title="PIN админа" --text="Введите PIN админа:" --window-icon="$icon_path")
    user_pin=$(zenity --entry --title="PIN пользователя" --text="Введите PIN пользователя:" --window-icon="$icon_path")

    if [ $? -ne 0 ]; then
        return
    fi

    if sudo ./kernel/rtadmin set-user-pin -p "$so_pin" -n "$user_pin" --auth-as so; then
        zenity --info --text="PIN пользователя установлен успешно."
    else
        zenity --error --text="Ошибка при установке PIN пользователя."
    fi
}

while true; do
command=$(
    zenity --forms --title="Админ Рутокен" --window-icon="$icon_path" \
        --text="Выберите действие из списка" \
        --add-combo="" \
        --combo-values="Установить PIN пользователя|Форматирование" \
)

if [ $? -ne 0 ]; then
    return
fi

case $command in
"Установить PIN пользователя")
    set_user_pin
    ;;
"Форматирование")
    format_device
    ;;
"Назад")
    continue 
    ;;
*)
    break  
    ;;
esac
done
