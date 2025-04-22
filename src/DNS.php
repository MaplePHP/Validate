<?php
/**
 * @Package:    MaplePHP - Luhn algorithm
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\Validate;

class DNS
{
    const ALOWED_DNS_TYPES = [
        DNS_A => 'DNS_A',         // Host Address
        DNS_CNAME => 'DNS_CNAME', // Canonical Name Record
        DNS_HINFO => 'DNS_HINFO', // Host Information
        DNS_CAA => 'DNS_CAA',     // Certification Authority Authorization
        DNS_MX => 'DNS_MX',       // Mail Exchange Record
        DNS_NS => 'DNS_NS',       // Name Server Record
        DNS_PTR => 'DNS_PTR',     // Pointer Record
        DNS_SOA => 'DNS_SOA',     // Start of Authority Record
        DNS_TXT => 'DNS_TXT',     // Text Record
        DNS_AAAA => 'DNS_AAAA',   // IPv6 Address Record
        DNS_SRV => 'DNS_SRV',     // Service Locator
        DNS_NAPTR => 'DNS_NAPTR', // Naming Authority Pointer
        DNS_A6 => 'DNS_A6',       // IPv6 Address Record (deprecated)
        DNS_ALL => 'DNS_ALL',     // All Records
        DNS_ANY => 'DNS_ANY',     // Any Records
    ];
    
    private string $host;

    public function __construct(string $host)
    {
        $this->host = $this->getHost($host);
    }

    /**
     * Check if the host is resolvable via DNS (MX, A, or AAAA record).
     *
     * @return bool
     */
    public function isResolvableHost(): bool
    {
        return $this->isMxRecord() || $this->isAddressRecord();
    }

    /**
     * Check if the host has a valid A or AAAA DNS record.
     *
     * @return bool
     */
    public function isAddressRecord(): bool
    {
        return checkdnsrr($this->host, 'A') || checkdnsrr($this->host, 'AAAA');
    }

    /**
     * Check if value is a valid mx host record
     *
     * @return bool
     */
    public function isMxRecord(): bool
    {
        // Argument 2 is MX by default
        return checkdnsrr($this->host);
    }


    /**
     * Check if value is a valid CNAME record
     *
     * A CNAME record is used to alias a domain to another domain.
     *
     * @return bool
     */
    public function isCnameRecord(): bool
    {
        return checkdnsrr($this->host, 'CNAME');
    }
    

    /**
     * Check if the host contains an 'A' record in the DNS.
     *
     * An 'A' record maps a domain name to an IPv4 address,
     * allowing the domain to be resolved to its corresponding IP.
     *
     * @return bool Returns true if an 'A' record exists, false otherwise.
     */
    public function isARecord(): bool
    {
        return checkdnsrr($this->host, 'A');
    }
    

    /**
     * Checks if the host contains an 'AAAA' record in the DNS.
     *
     * An 'AAAA' record maps a domain name to an IPv6 address,
     * allowing the domain to be resolved to its corresponding IPv6 address.
     *
     * @return bool Returns true if an 'AAAA' record exists, false otherwise.
     */
    public function isAaaaRecord(): bool
    {
        return checkdnsrr($this->host, 'AAAA');
    }


    /**
     * Check if the host contains an 'NS' record in the DNS.
     *
     * An 'NS' (Name Server) record delegates a domain or subdomain to a set of name servers.
     *
     * @return bool Returns true if an 'NS' record exists, false otherwise.
     */
    public function isNsRecord(): bool
    {
        return checkdnsrr($this->host, 'NS');
    }
    

    /**
     * Check if the host contains a 'SOA' record in the DNS.
     *
     * A 'SOA' (Start of Authority) record provides information about the
     * domain's administrative information, such as the primary DNS server,
     * the email of the domain administrator, and other essential info.
     *
     * @return bool Returns true if a 'SOA' record exists, false otherwise.
     */
    public function isSoaRecord(): bool
    {
        return checkdnsrr($this->host, 'SOA');
    }
    

    /**
     * Check if the host contains a 'TXT' record in the DNS.
     *
     * A 'TXT' record is primarily used to store human-readable data related to the domain,
     * such as SPF records, or other text-based information.
     *
     * @return bool Returns true if a 'TXT' record exists, false otherwise.
     */
    public function isTxtRecord(): bool
    {
        return checkdnsrr($this->host, 'TXT');
    }
    

    /**
     * Check if the host contains an 'SRV' record in the DNS.
     *
     * An 'SRV' record is used to define the location (hostname and port)
     * of servers for specified services.
     *
     * @return bool Returns true if an 'SRV' record exists, false otherwise.
     */
    public function isSrvRecord(): bool
    {
        return checkdnsrr($this->host, 'SRV');
    }
    

    /**
     * Check if the host contains a 'NAPTR' record in the DNS.
     *
     * A 'NAPTR' (Naming Authority Pointer) record is used for applications in
     * Internet telephony to map phone numbers to domain names or define other service-specific rules.
     *
     * @return bool Returns true if a 'NAPTR' record exists, false otherwise.
     */
    public function isNaptrRecord(): bool
    {
        return checkdnsrr($this->host, 'NAPTR');
    }
    

    /**
     * Check if the host contains an 'A6' record in the DNS.
     *
     * An 'A6' record was used in earlier IPv6 implementations to map a domain name to an IPv6 address,
     * but it has been deprecated in favor of 'AAAA' records.
     *
     * @return bool Returns true if an 'A6' record exists, false otherwise.
     */
    public function isA6Record(): bool
    {
        return checkdnsrr($this->host, 'A6');
    }
    

    /**
     * Check if the host contains any valid DNS record of any type.
     *
     * This method checks for the existence of any DNS record for the given host
     * without specifying any particular record type.
     *
     * @return bool Returns true if any valid DNS record exists, false otherwise.
     */
    public function isAnyRecord(): bool
    {
        return checkdnsrr($this->host, 'ANY');
    }
    

    /**
     * Check if the host contains all DNS records.
     *
     * This method checks for the existence of all possible DNS records associated
     * with the given host. It supports various record types like A, AAAA, CNAME, NS,
     * SOA, and others.
     *
     * @return bool Returns true if all DNS records exist, false otherwise.
     */
    public function isAllRecord(): bool
    {
        return checkdnsrr($this->host, 'ALL');
    }
    

    /**
     * Check if the host contains a 'CAA' record in the DNS.
     *
     * A 'CAA' (Certification Authority Authorization) record is used to specify
     * which certificate authorities (CAs) are allowed to issue certificates for a domain.
     *
     * @return bool Returns true if a 'CAA' record exists, false otherwise.
     */
    public function isCaaRecord(): bool
    {
        return checkdnsrr($this->host, 'CAA');
    }
    

    /**
     * Check if the host contains a 'PTR' record in the DNS.
     *
     * A 'PTR' (Pointer) record is used for reverse DNS lookups,
     * often translating an IP address to a hostname.
     *
     * @return bool Returns true if a 'PTR' record exists, false otherwise.
     */
    public function isPtrRecord(): bool
    {
        return checkdnsrr($this->host, 'PTR');
    }


    /**
     * Check if the host contains an 'HINFO' record in the DNS.
     *
     * An 'HINFO' (Host Information) record provides information about the hardware
     * and operating system of a host. This type of record is rarely used due to
     * security concerns.
     *
     * @return bool Returns true if an 'HINFO' record exists, false otherwise.
     */
    public function isHinfoRecord(): bool
    {
        return checkdnsrr($this->host, 'HINFO');
    }

    /**
     * Fetch DNS Resource Records associated with a hostname
     *
     * This method retrieves DNS records of a specific type for the host
     * and returns them as an array if found. Supported DNS types include:
     * DNS_A, DNS_CNAME, DNS_HINFO, DNS_CAA, DNS_MX, DNS_NS, DNS_PTR, DNS_SOA,
     * DNS_TXT, DNS_AAAA, DNS_SRV, DNS_NAPTR, DNS_A6, DNS_ALL, or DNS_ANY.
     *
     * @param int $type The DNS record type to match.
     * @return array|false Returns an array of matched DNS records, or false if none are found.
     *
     * @throws \InvalidArgumentException If an invalid or unsupported DNS type is provided.
     */
    public function getDnsRecordForType(int $type): array|false
    {
        if(!isset(self::ALOWED_DNS_TYPES[$type])) {
            throw new \InvalidArgumentException('Invalid DNS type. Use one of ' . implode(', ', self::ALOWED_DNS_TYPES) . '.');
        }
        $result = dns_get_record($this->host, $type);
        if (is_array($result) && count($result) > 0) {
            return $result;
        }
        return false;
    }
    
    /**
     * Get hosts (used for DNS checks)
     * 
     * @noinspection PhpComposerExtensionStubsInspection
     * @param string $host
     * @return string
     */
    protected function getHost(string $host): string
    {
        if (!defined('INTL_IDNA_VARIANT_2003')) {
            define('INTL_IDNA_VARIANT_2003', 0);
        }
        $variant = (defined('INTL_IDNA_VARIANT_UTS46')) ? INTL_IDNA_VARIANT_UTS46 : INTL_IDNA_VARIANT_2003;
        return rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.') . '.';
    }
}
