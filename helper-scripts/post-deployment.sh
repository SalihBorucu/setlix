git pull origin deploy --no-edit --no-rebase

if [ $? -eq 0 ]; then
  echo "Pulling Complete"

  php artisan migrate

  else

  echo "Not finished pulling"
fi
