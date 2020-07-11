<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<?aid style="50" type="document" readerVersion="6.0" featureSet="257" product="15.0(323)" ?>
<Document xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging" DOMVersion="15.0" Self="d" StoryList="u3a38b u3a5e7 u3a5fe u3b70c u3a615 u3a65e u3a62c u3a690 u3a675 u3a643 u3a9b9 {{ join(' ', $stories) }}" Name="ev3" ZeroPoint="0 0" ActiveLayer="ub9" CMYKProfile="Coated FOGRA39 (ISO 12647-2:2004)" RGBProfile="sRGB IEC61966-2.1" SolidColorIntent="UseColorSettings" AfterBlendingIntent="UseColorSettings" DefaultImageIntent="UseColorSettings" RGBPolicy="PreserveEmbeddedProfiles" CMYKPolicy="CombinationOfPreserveAndSafeCmyk" AccurateLABSpots="false">
    <Language Self="Language/$ID/de_DE_2006" Name="$ID/de_DE_2006" SingleQuotes="‚‘" DoubleQuotes="„“" PrimaryLanguageName="$ID/de_DE_2006" SublanguageName="$ID/" Id="275" HyphenationVendor="Duden" SpellingVendor="Duden" />
    <idPkg:Graphic src="Resources/Graphic.xml" />
    <idPkg:Fonts src="Resources/Fonts.xml" />
    <idPkg:Styles src="Resources/Styles.xml" />
    <NumberingList Self="NumberingList/$ID/[Default]" Name="$ID/[Default]" ContinueNumbersAcrossStories="false" ContinueNumbersAcrossDocuments="false" />
    <idPkg:Preferences src="Resources/Preferences.xml" />
    <Layer Self="ub9" Name="14_15_Gottesdienste" Visible="true" Locked="false" IgnoreWrap="true" ShowGuides="true" LockGuides="false" UI="true" Expendable="true" Printable="true">
        <Properties>
            <LayerColor type="enumeration">DarkGreen</LayerColor>
        </Properties>
    </Layer>
    <Layer Self="u1fe" Name="allgemein" Visible="true" Locked="true" IgnoreWrap="false" ShowGuides="true" LockGuides="false" UI="true" Expendable="true" Printable="true">
        <Properties>
            <LayerColor type="enumeration">Red</LayerColor>
        </Properties>
    </Layer>
    <idPkg:MasterSpread src="MasterSpreads/MasterSpread_uba.xml" />
    <idPkg:Spread src="Spreads/Spread_u35ee1.xml" />
    <idPkg:BackingStory src="XML/BackingStory.xml" />
    <idPkg:Story src="Stories/Story_u3a38b.xml" />
    <idPkg:Story src="Stories/Story_u3a5e7.xml" />
    <idPkg:Story src="Stories/Story_u3a5fe.xml" />
    <idPkg:Story src="Stories/Story_u3b70c.xml" />
    <idPkg:Story src="Stories/Story_u3a615.xml" />
    <idPkg:Story src="Stories/Story_u3a65e.xml" />
    <idPkg:Story src="Stories/Story_u3a62c.xml" />
    <idPkg:Story src="Stories/Story_u3a690.xml" />
    <idPkg:Story src="Stories/Story_u3a675.xml" />
    <idPkg:Story src="Stories/Story_u3a643.xml" />
    <idPkg:Story src="Stories/Story_u3a9b9.xml" />
    <idPkg:Story src="Stories/Story_u3a5d0.xml" />
    <idPkg:Story src="Stories/Story_u3a4a2.xml" />
    <idPkg:Story src="Stories/Story_u3a5b8.xml" />
    <idPkg:Story src="Stories/Story_u3ab22.xml" />
    <idPkg:Story src="Stories/Story_u3a98a.xml" />
    <idPkg:Story src="Stories/Story_u3a9a2.xml" />
    <idPkg:Story src="Stories/Story_u36be1.xml" />
    <idPkg:Story src="Stories/Story_u36bf8.xml" />
@foreach($stories as $story)<idPkg:Story src="Stories/Story_{{ $story }}.xml" />
    @endforeach
</Document>
