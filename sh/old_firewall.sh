#!/bin/sh
#
# firewall.sh

IPT=/usr/sbin/iptables
SYS=/usr/sbin/sysctl
SERVICE=/sbin/service


# internet
IFACE_INET=enp0s3


# Réseau local
IFACE_LAN=enp0s3
IFACE_LAN_IP=192.168.43.0/24
#IFACE_LAN_IP=192.168.1.0/24

# Activer le relais des paquets ? (yes/no)
MASQ=yes

# Tout accepter
$IPT -t filter -P INPUT ACCEPT
$IPT -t filter -P FORWARD ACCEPT
$IPT -t filter -P OUTPUT ACCEPT
$IPT -t nat -P PREROUTING ACCEPT
$IPT -t nat -P POSTROUTING ACCEPT
$IPT -t nat -P OUTPUT ACCEPT
$IPT -t mangle -P PREROUTING ACCEPT
$IPT -t mangle -P INPUT ACCEPT
$IPT -t mangle -P FORWARD ACCEPT
$IPT -t mangle -P OUTPUT ACCEPT
$IPT -t mangle -P POSTROUTING ACCEPT

# Remettre les compteurs à zéro
$IPT -t filter -Z
$IPT -t nat -Z
$IPT -t mangle -Z

# Supprimer toutes les règles actives et les chaînes personnalisées
$IPT -t filter -F
$IPT -t filter -X
$IPT -t nat -F
$IPT -t nat -X
$IPT -t mangle -F
$IPT -t mangle -X

# Désactiver le relais des paquets
$SYS -q -w net.ipv4.ip_forward=0


# Politique par défaut
$IPT -P INPUT DROP
$IPT -P FORWARD DROP
$IPT -P OUTPUT ACCEPT

# Faire confiance à nous-mêmes ;o)
$IPT -A INPUT -i lo -j ACCEPT

# Ping
$IPT -A INPUT -p icmp --icmp-type echo-request -j ACCEPT
$IPT -A INPUT -p icmp --icmp-type time-exceeded -j ACCEPT
$IPT -A INPUT -p icmp --icmp-type destination-unreachable -j ACCEPT


# Connexions établies
$IPT -A INPUT -m state --state ESTABLISHED -j ACCEPT


# SSH 
#$IPT -A INPUT -p tcp -i $IFACE_INET --dport 22 -j ACCEPT
#$IPT -A INPUT -p tcp -i $IFACE_LAN --dport 22 -j ACCEPT

# SSH illimité 
$IPT -A INPUT -p tcp -s 192.168.1.2 -i $IFACE_INET --dport 22 \
  -j ACCEPT
$IPT -A INPUT -p tcp -s 192.168.43.95 -i $IFACE_INET --dport 22 \
  -j ACCEPT

# SSH limité
$IPT -A INPUT -p tcp -i $IFACE_INET --dport 22 -m state \
  --state NEW -m recent --set --name SSH
$IPT -A INPUT -p tcp -i $IFACE_INET --dport 22 -m state \
  --state NEW -m recent --update --seconds 300 --hitcount 2 \
  --rttl --name SSH -j DROP
$IPT -A INPUT -p tcp -i $IFACE_INET --dport 22 -j ACCEPT



# apache httpd /  autoriser la connexion a notre serveur local sur le port 80 protocol tcp
$IPT -A INPUT -p tcp -i $IFACE_LAN --dport 8000 -j ACCEPT
$IPT -A INPUT -p tcp -i $IFACE_LAN --dport 80 -j ACCEPT
$IPT -A INPUT -p tcp -i $IFACE_INET --dport 8000 -j ACCEPT
$IPT -A INPUT -p tcp -i $IFACE_INET --dport 80 -j ACCEPT


# Dnsmasq 
$IPT -A INPUT -p tcp -i $IFACE_LAN --dport 53 -j ACCEPT
$IPT -A INPUT -p udp -i $IFACE_LAN --dport 53 -j ACCEPT
$IPT -A INPUT -p udp -i $IFACE_LAN --dport 67:68 -j ACCEPT

# Activer le relais des paquets
if [ $MASQ = 'yes' ]; then
  $IPT -t nat -A POSTROUTING -o $IFACE_INET -s $IFACE_LAN_IP \
    -j MASQUERADE
  $SYS -q -w net.ipv4.ip_forward=1
fi




# Enregistrer les connexions refusées
$IPT -A INPUT -m limit --limit 2/min -j LOG \
  --log-prefix "++ IPv4 packet rejected ++ "
$IPT -A INPUT -j DROP
#$IPT -A INPUT -j LOG --log-prefix "++ IPv4 packet rejected ++ "

# Enregistrer la configuration
$SERVICE iptables save

