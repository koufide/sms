<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OutgoingRepository")
 */
class Outgoing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $de;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $a;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $messageId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $statusSendsms;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $text;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sendsmsAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $resultsReports;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $letest = [];

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $bulkId;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sendGroupid;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $sendGroupname;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sendId;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $sendName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $sendDescription;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $smsCount;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $reportSentat;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $reportDoneat;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $reportMccmnc;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reportPricepermessage;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $reportCurrency;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reportStatusGroupid;

    /** 
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reportStatusGroupname;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reportStatusId;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $reportStatusName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $reportStatusDescription;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)  
     */
    private $reportErrorGroupid;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reportErrorGroupname;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reportErrorId;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reportErrorName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reportErrorDescription;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $reportErrorPermanent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $resultsLogs;

    /**
     * @ORM\Column(type="smallint")
     */
    private $TENTATIVE;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $applic;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDe(): ?string
    {
        return $this->de;
    }

    public function setDe(string $de): self
    {
        $this->de = $de;

        return $this;
    }

    public function getA(): ?string
    {
        return $this->a;
    }

    public function setA(string $a): self
    {
        $this->a = $a;

        return $this;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function setMessageId(string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getSendsmsAt(): ?\DateTimeInterface
    {
        return $this->sendsmsAt;
    }

    public function setSendsmsAt(?\DateTimeInterface $sendsmsAt): self
    {
        $this->sendsmsAt = $sendsmsAt;

        return $this;
    }

    public function getResultsReports(): ?string
    {
        return $this->resultsReports;
    }

    public function setResultsReports(?string $resultsReports): self
    {
        $this->resultsReports = $resultsReports;

        return $this;
    }

    public function getStatusSendsms(): ?string
    {
        return $this->statusSendsms;
    }

    public function setStatusSendsms(?string $statusSendsms): self
    {
        $this->statusSendsms = $statusSendsms;

        return $this;
    }

    public function getLetest(): ?array
    {
        return $this->letest;
    }

    public function setLetest(?array $letest): self
    {
        $this->letest = $letest;

        return $this;
    }

    public function getBulkId(): ?string
    {
        return $this->bulkId;
    }

    public function setBulkId(?string $bulkId): self
    {
        $this->bulkId = $bulkId;

        return $this;
    }

    public function getSendGroupid(): ?int
    {
        return $this->sendGroupid;
    }

    public function setSendGroupid(?int $sendGroupid): self
    {
        $this->sendGroupid = $sendGroupid;

        return $this;
    }

    public function getSendGroupname(): ?string
    {
        return $this->sendGroupname;
    }

    public function setSendGroupname(?string $sendGroupname): self
    {
        $this->sendGroupname = $sendGroupname;

        return $this;
    }

    public function getSendId(): ?int
    {
        return $this->sendId;
    }

    public function setSendId(?int $sendId): self
    {
        $this->sendId = $sendId;

        return $this;
    }

    public function getSendName(): ?string
    {
        return $this->sendName;
    }

    public function setSendName(?string $sendName): self
    {
        $this->sendName = $sendName;

        return $this;
    }

    public function getSendDescription(): ?string
    {
        return $this->sendDescription;
    }

    public function setSendDescription(?string $sendDescription): self
    {
        $this->sendDescription = $sendDescription;

        return $this;
    }

    public function getSmsCount(): ?int
    {
        return $this->smsCount;
    }

    public function setSmsCount(?int $smsCount): self
    {
        $this->smsCount = $smsCount;

        return $this;
    }

    public function getReportSentat(): ?\DateTimeInterface
    {
        return $this->reportSentat;
    }

    public function setReportSentat(?\DateTimeInterface $reportSentat): self
    {
        $this->reportSentat = $reportSentat;

        return $this;
    }

    public function getReportDoneat(): ?\DateTimeInterface
    {
        return $this->reportDoneat;
    }

    public function setReportDoneat(?\DateTimeInterface $reportDoneat): self
    {
        $this->reportDoneat = $reportDoneat;

        return $this;
    }

    public function getReportMccmnc(): ?string
    {
        return $this->reportMccmnc;
    }

    public function setReportMccmnc(?string $reportMccmnc): self
    {
        $this->reportMccmnc = $reportMccmnc;

        return $this;
    }

    public function getReportPricepermessage(): ?int
    {
        return $this->reportPricepermessage;
    }

    public function setReportPricepermessage(?int $reportPricepermessage): self
    {
        $this->reportPricepermessage = $reportPricepermessage;

        return $this;
    }

    public function getReportCurrency(): ?string
    {
        return $this->reportCurrency;
    }

    public function setReportCurrency(?string $reportCurrency): self
    {
        $this->reportCurrency = $reportCurrency;

        return $this;
    }

    public function getReportStatusGroupid(): ?int
    {
        return $this->reportStatusGroupid;
    }

    public function setReportStatusGroupid(?int $reportStatusGroupid): self
    {
        $this->reportStatusGroupid = $reportStatusGroupid;

        return $this;
    }

    public function getReportStatusGroupname(): ?string
    {
        return $this->reportStatusGroupname;
    }

    public function setReportStatusGroupname(?string $reportStatusGroupname): self
    {
        $this->reportStatusGroupname = $reportStatusGroupname;

        return $this;
    }

    public function getReportStatusId(): ?int
    {
        return $this->reportStatusId;
    }

    public function setReportStatusId(?int $reportStatusId): self
    {
        $this->reportStatusId = $reportStatusId;

        return $this;
    }

    public function getReportStatusName(): ?string
    {
        return $this->reportStatusName;
    }

    public function setReportStatusName(?string $reportStatusName): self
    {
        $this->reportStatusName = $reportStatusName;

        return $this;
    }

    public function getReportStatusDescription(): ?string
    {
        return $this->reportStatusDescription;
    }

    public function setReportStatusDescription(?string $reportStatusDescription): self
    {
        $this->reportStatusDescription = $reportStatusDescription;

        return $this;
    }

    public function getReportErrorGroupid(): ?string
    {
        return $this->reportErrorGroupid;
    }

    public function setReportErrorGroupid(?string $reportErrorGroupid): self
    {
        $this->reportErrorGroupid = $reportErrorGroupid;

        return $this;
    }

    public function getReportErrorGroupname(): ?string
    {
        return $this->reportErrorGroupname;
    }

    public function setReportErrorGroupname(?string $reportErrorGroupname): self
    {
        $this->reportErrorGroupname = $reportErrorGroupname;

        return $this;
    }

    public function getReportErrorId(): ?int
    {
        return $this->reportErrorId;
    }

    public function setReportErrorId(?int $reportErrorId): self
    {
        $this->reportErrorId = $reportErrorId;

        return $this;
    }

    public function getReportErrorName(): ?string
    {
        return $this->reportErrorName;
    }

    public function setReportErrorName(?string $reportErrorName): self
    {
        $this->reportErrorName = $reportErrorName;

        return $this;
    }

    public function getReportErrorDescription(): ?string
    {
        return $this->reportErrorDescription;
    }

    public function setReportErrorDescription(?string $reportErrorDescription): self
    {
        $this->reportErrorDescription = $reportErrorDescription;

        return $this;
    }

    public function getReportErrorPermanent(): ?string
    {
        return $this->reportErrorPermanent;
    }

    public function setReportErrorPermanent(?string $reportErrorPermanent): self
    {
        $this->reportErrorPermanent = $reportErrorPermanent;

        return $this;
    }

    public function getResultsLogs(): ?string
    {
        return $this->resultsLogs;
    }

    public function setResultsLogs(?string $resultsLogs): self
    {
        $this->resultsLogs = $resultsLogs;

        return $this;
    }

    public function getTENTATIVE(): ?int
    {
        return $this->TENTATIVE;
    }

    public function setTENTATIVE(int $TENTATIVE): self
    {
        $this->TENTATIVE = $TENTATIVE;

        return $this;
    }

    public function getApplic(): ?string
    {
        return $this->applic;
    }

    public function setApplic(?string $applic): self
    {
        $this->applic = $applic;

        return $this;
    }



}
