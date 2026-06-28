package main

import (
	"fmt"
	"net"
	"strconv"
)

func isValidHost(host string) bool {
	return net.ParseIP(host) != nil
}

func isValidPort(port string) bool {
	intPort, err := strconv.Atoi(port)
	if err != nil {
		return false
	}

	const MIN_PORT = 1
	const MAX_PORT = 65535
	return intPort > MIN_PORT && intPort < MAX_PORT
}

func validateAddress(host, port string) error {
	if !isValidHost(host) {
		return fmt.Errorf("invalid host: %v", host)
	}
	if !isValidPort(port) {
		return fmt.Errorf("invalid port: %v", port)
	}
	return nil
}
