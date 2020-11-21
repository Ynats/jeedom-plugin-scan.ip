PROGRESS_FILE=/tmp/scan_ip/in_progress
OUI_FILE=/tmp/scan_ip/dependancy
if [ ! -z $1 ]; then
	PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "********************************************************"
echo "*             Installation des dépendances             *"
echo "********************************************************"
echo 0 > ${PROGRESS_FILE}
apt-get update
echo 40 > ${PROGRESS_FILE}
sudo apt-get install -y arp-scan
echo 50 > ${PROGRESS_FILE}
sudo apt-get install -y iproute2
echo 60 > ${PROGRESS_FILE}
sudo apt-get install -y net-tools
echo 70 > ${PROGRESS_FILE}
sudo apt-get install -y wakeonlan 
echo 80 > ${PROGRESS_FILE}
sudo apt-get install -y etherwake 
echo 100 > ${PROGRESS_FILE}
echo "********************************************************"
echo "*             Installation terminée                    *"
echo "********************************************************"
rm ${PROGRESS_FILE}