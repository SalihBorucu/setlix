#!/bin/sh

echo "Deploying application ..."

CURRENT=$(pwd)
BASENAME=$(basename "$CURRENT")

if [ "$BASENAME" != "deployer-setlix" ]; then
  echo "Wrong Directory"
  kill 0
fi

git checkout main
git pull origin main
git checkout deploy
git_changes=$(git diff main --name-only)
git pull origin main --no-edit --no-rebase

if echo "$git_changes" | grep 'deploy.sh'; then
  echo "DEPLOYER SCRIPT CHANGED"
  kill 0
fi

if [ $? -ne 0 ]; then
  echo "SOMETHING WENT WRONG WITH PULLING"
  kill 0
fi

vite_changed=false

if echo "$git_changes" | grep 'resources/js\|resources/css'; then
  vite_changed=true
fi

if [ "$vite_changed" = true ]; then
  npm install
  npm run prod
#  aws s3 sync public/dist/assets s3://deftship-public/production/assets
fi

#git config --local user.email "salih"
#git config --local user.name "Kodeas CD"
git add .
git add -f public/dist/manifest.json
git commit -m "Build front-end assets 📦 🚀"
git push -f origin deploy

if [ $? -eq 0 ]; then

ssh -t root@51.89.139.7 << EOF
  cd /var/www/setlix;
  ./helper-scripts/post-deployment.sh
EOF

fi

