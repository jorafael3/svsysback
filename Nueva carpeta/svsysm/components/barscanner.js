import React, { Component } from 'react';
import { View, Text, TouchableOpacity } from 'react-native';
import { CameraKitCameraScreen, CameraKitCamera, CameraKitGalleryView } from 'react-native-camera-kit';


class BarcodeScanner extends Component {
    constructor(props) {
      super(props);
      this.state = {
        scannedBarcode: '',
      };
    }
  
    onBarcodeScan = ({ nativeEvent }) => {
      this.setState({ scannedBarcode: nativeEvent.codeStringValue });
    };
  
    render() {
      return (
        <View style={{ flex: 1 }}>
          <CameraKitCameraScreen
            showFrame={true}
            scanBarcode={true}
            laserColor={'red'}
            frameColor={'yellow'}
            onReadCode={event => this.onBarcodeScan(event)}
          />
          <Text style={{ marginTop: 20 }}>Barcode: {this.state.scannedBarcode}</Text>
        </View>
      );
    }
  }

export default BarcodeScanner;