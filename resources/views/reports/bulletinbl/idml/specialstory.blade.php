<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<idPkg:Story xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging" DOMVersion="15.0">
    <Story Self="u3a9b9" UserText="true" IsEndnoteStory="false" AppliedTOCStyle="n" TrackChanges="false" StoryTitle="$ID/" AppliedNamedGrid="n">
        <StoryPreference OpticalMarginAlignment="false" OpticalMarginSize="12" FrameType="TextFrameType" StoryOrientation="Horizontal" StoryDirection="LeftToRightDirection" />
        <InCopyExportOption IncludeGraphicProxies="true" IncludeAllResources="false" />
        @foreach ($specialServices as $group)
            <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Kasten%3aKastenüberschrift" LeftIndent="2.834645669291339" SpaceBefore="0" Justification="LeftAlign">
                <Properties>
                    <TabList type="list">
                        <ListItem type="record">
                            <Alignment type="enumeration">RightAlign</Alignment>
                            <AlignmentCharacter type="string">.</AlignmentCharacter>
                            <Leader type="string"></Leader>
                            <Position type="unit">192.75590551181102</Position>
                        </ListItem>
                    </TabList>
                </Properties>
                <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]" FillColor="Color/C=0 M=0 Y=0 K=75" PointSize="10" HorizontalScale="92" BaselineShift="1" Capitalization="Normal">
                    <Properties>
                        <Leading type="unit">12</Leading>
                    </Properties>
                    <Content>{{ $group['options']['group'] }}</Content>
                    <Br />
                </CharacterStyleRange>
                @if ($group['options']['sameTime'] && (count($group['services']) > 1))
                    <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]" FillColor="Color/C=0 M=0 Y=0 K=75" PointSize="10" HorizontalScale="92" BaselineShift="1" Capitalization="Normal">
                        <Properties>
                            <Leading type="unit">12</Leading>
                        </Properties>
                        <Content>jeweils {{ $group['options']['time'] }}</Content>
                        <Br />
                    </CharacterStyleRange>
                @endif
                @foreach($group['services'] as $service)
                    <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]" FillColor="Color/C=0 M=0 Y=0 K=75" FontStyle="Regular" PointSize="10" Ligatures="false" BaselineShift="1" Capitalization="Normal" OTFContextualAlternate="false">
                        <Properties>
                            <Leading type="unit">12</Leading>
                        </Properties>
                        <Content>• <?ACE 7?></Content>
                    </CharacterStyleRange>
                    <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]" FillColor="Color/C=0 M=0 Y=0 K=75" FontStyle="Regular" PointSize="10" HorizontalScale="94" Ligatures="false" BaselineShift="1" Capitalization="Normal" OTFContextualAlternate="false">
                        <Properties>
                            <Leading type="unit">12</Leading>
                        </Properties>
                        <Content>{{ $service->date->formatLocalized('%A, %d. %B') }}{!! ((!$group['options']['sameTime']) || (count($group['services']) == 1)) ? ', '.$service->timeText(true, '.', false, false) : '' !!}{{ ((!$group['options']['sameLocation']) && (trim($service->locationText()) != '')) ? ', '.$service->locationText() : ''  }}</Content>
                        <Br />
                    </CharacterStyleRange>
                @endforeach
                <Br />
            </ParagraphStyleRange>
        @endforeach
    </Story>
</idPkg:Story>
